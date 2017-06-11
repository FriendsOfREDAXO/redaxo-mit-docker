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

    // init db
    $err .= rex_setup::checkDb($config, false);
    // $err .= rex_setup_importer::prepareEmptyDb(); // not required!
    $err .= rex_setup_importer::verifyDbSchema();

    if ($err != '') {
        echo $err;
        exit(10);
    }

    $config['setup'] = false;
    if (rex_file::putConfig($configFile, $config)) {
        echo 'instance setup successfull', PHP_EOL;
        exit(0);
    }
    echo 'instance setup failure', PHP_EOL;
    exit(1);
}

echo 'instance setup not necessary', PHP_EOL;
exit(0);