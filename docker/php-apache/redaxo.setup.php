#!/usr/bin/php
<?php
// adapted from https://github.com/redaxo/redaxo/blob/master/redaxo/src/addons/tests/bin/setup.php

// ---- bootstrap REX

$REX = [];
$REX['REDAXO'] = true;
$REX['HTDOCS_PATH'] = '../';
$REX['BACKEND_FOLDER'] = 'redaxo';

// bootstrap core
require 'src/core/boot.php';

// bootstrap addons
include_once rex_path::core('packages.php');

// run setup, if instance not already prepared
if (rex::isSetup()) {
    $err = '';

    // read initial config
    $configFile = rex_path::coreData('config.yml');
    $config = array_merge(
        rex_file::getConfig(rex_path::core('default.config.yml')),
        rex_file::getConfig($configFile)
    );

    // connect to db (repeat if not ready yet)
    $dbConnected = false;
    for ($i = 1, $max = 10; $i <= $max; ++$i) {
        $check = rex_setup::checkDb($config, false);
        if ($check == '') {
            $dbConnected = true;
            echo 'Established database connection. (' . $i . ')', PHP_EOL;
            break;
        }
        sleep(3);
    }
    if (!$dbConnected) {
        $err .= 'Failed to connect database!';
    }

    // init db
    $err .= rex_setup_importer::prepareEmptyDb();
    $err .= rex_setup_importer::verifyDbSchema();

    // create admin user
    $ga = rex_sql::factory();
    $ga->setQuery('select * from ' . rex::getTablePrefix() . 'user where login = ? ', ['admin']);

    if ($ga->getRows() > 0) {
        $err .= 'Admin user already exists!';
    } else {
        $user = rex_sql::factory();
        // $user->setDebug();
        $user->setTable(rex::getTablePrefix() . 'user');
        $user->setValue('name', 'Administrator');
        $user->setValue('login', 'admin');
        $user->setValue('password', rex_login::passwordHash('admin'));
        $user->setValue('admin', 1);
        $user->addGlobalCreateFields('setup');
        $user->setValue('status', '1');
        try {
            $user->insert();
            echo 'Admin user successfully created.', PHP_EOL;
        } catch (rex_sql_exception $e) {
            $err .= 'Could not create admin user!';
        }
    }

    // provide errors
    if ($err != '') {
        echo $err;
        exit(10);
    }

    // save config and finish setup
    $config['setup'] = false;
    if (rex_file::putConfig($configFile, $config)) {
        echo 'REDAXO setup successfull.', PHP_EOL;
        exit(0);
    }
    echo 'REDAXO setup failure.', PHP_EOL;
    exit(1);
}

echo 'REDAXO setup not necessary.', PHP_EOL;
exit(0);
