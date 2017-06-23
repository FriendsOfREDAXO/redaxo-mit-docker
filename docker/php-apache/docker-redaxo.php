#!/usr/bin/php
<?php

// get arguments from command line
$options = getopt('', array('user::', 'password::', 'demo::'));


// ------------------------------------------------------------------------------------------------
// bootstrap REX
// ------------------------------------------------------------------------------------------------

$REX = [];
$REX['REDAXO'] = true;
$REX['HTDOCS_PATH'] = '../';
$REX['BACKEND_FOLDER'] = 'redaxo';

// bootstrap core
require 'src/core/boot.php';

// bootstrap addons
include_once rex_path::core('packages.php');


// ------------------------------------------------------------------------------------------------
// install REDAXO
// adapted from https://github.com/redaxo/redaxo/blob/master/redaxo/src/addons/tests/bin/setup.php
// ------------------------------------------------------------------------------------------------

if ($options['user'] && $options['password']) {

    if (rex::isSetup()) {
        $err = '';

        // read initial config
        $configFile = rex_path::coreData('config.yml');
        $config = array_merge(
            rex_file::getConfig(rex_path::core('default.config.yml')),
            rex_file::getConfig($configFile)
        );

        // connect to db (repeat if not ready yet)
        // remember the docker database container takes some time to init!
        $dbConnected = false;
        for ($i = 1, $max = 15; $i <= $max; ++$i) {
            $check = rex_setup::checkDb($config, false);
            if ($check == '') {
                $dbConnected = true;
                echo '✅ Established database connection. (' . $i . ')', PHP_EOL;
                break;
            }
            sleep(4);
        }
        if (!$dbConnected) {
            echo '✋ Failed to connect database! Skip setup.';
            exit(1);
        }

        // init db
        $empty = rex_setup_importer::verifyDbSchema();
        if (!$empty) {
            echo '✋ Database not empty! Skip setup.';
            exit(1);
        }
        $err .= rex_setup_importer::prepareEmptyDb();
        $err .= rex_setup_importer::verifyDbSchema();

        if ($err != '') {
            echo $err;
            exit(1);
        }

        // create admin user
        $user = rex_sql::factory();
        $user->setTable(rex::getTablePrefix() . 'user');
        $user->setValue('name', 'Administrator');
        $user->setValue('login', $options['user']);
        $user->setValue('password', rex_login::passwordHash($options['password']));
        $user->setValue('admin', 1);
        $user->addGlobalCreateFields('setup');
        $user->setValue('status', '1');
        try {
            $user->insert();
            echo '✅ Admin user successfully created.', PHP_EOL;
        } catch (rex_sql_exception $e) {
            echo '✋ Could not create admin user!', PHP_EOL;
        }

        // save config and finish setup
        $config['setup'] = false;
        if (rex_file::putConfig($configFile, $config)) {
            echo '🚀 REDAXO setup successful.', PHP_EOL;
            exit(0);
        }

        echo '✋ REDAXO setup failure!', PHP_EOL;
        exit(1);
    }

    echo '✋ REDAXO already installed! Skip setup.', PHP_EOL;
    exit(1);
}


// ------------------------------------------------------------------------------------------------
// install demos
// ------------------------------------------------------------------------------------------------

if ($options['demo']) {

    if (!rex::isSetup()) {

        // get demos config
        $config = rex_file::getConfig('demos.yml');
        if ($config[$options['demo']]) {

            // fetch packages from redaxo.org
            $packages = rex_install_webservice::getJson('packages/');


            // step 1/4
            // fetch and extract addons
            foreach ($config[$options['demo']]['addons'] as $addon) {

                // check if addon exists
                if (rex_addon::exists($addon['addonkey'])) {
                    continue;
                }

                // read addon data from config
                $file = $packages[$addon['addonkey']]['files'][$addon['file']];
                if ($file) {

                    // fetch package
                    try {
                        $archivefile = rex_install_webservice::getArchive($file['path']);
                    } catch (rex_functional_exception $e) {
                        echo '✋ Could not fetch addon ' . $addon['addonkey'] . ': ' . $e->getMessage(), PHP_EOL;
                        continue;
                    }

                    // validate checksum
                    if ($file['checksum'] != md5_file($archivefile)) {
                        echo '✋ Checksum for ' . $addon['addonkey'] . ' invalid!', PHP_EOL;
                        continue;
                    }

                    // extract addon
                    if (!rex_install_archive::extract($archivefile, rex_path::addon($addon['addonkey']), $addon['addonkey'])) {
                        rex_dir::delete(rex_path::addon($addon['addonkey']));
                        echo '✋ Could not extract addon ' . $addon['addonkey'] . '!', PHP_EOL;
                        continue;
                    }
                    rex_package_manager::synchronizeWithFileSystem();
                    rex_install_packages::addedPackage($addon['addonkey']);
                }
            }


            // step 2/4
            // install and activate addons
            foreach ($config[$options['demo']]['activate'] as $addon) {

                $package = rex_package::get($addon);
                if ($package instanceof rex_null_package) {
                    echo '✋ Addon ' . $addon . ' does not exist!', PHP_EOL;
                    continue;
                }
                if ($package->isAvailable()) {
                    echo '✋ Addon ' . $addon . ' is already activated!', PHP_EOL;
                    continue;
                }
                if (!$package->isInstalled()) {
                    $manager = rex_package_manager::factory($package);
                    if (!$manager->install()) {
                        echo '✋ Could not install addon ' . $addon . '!', PHP_EOL;
                        continue;
                    }
                    if (!$manager->activate()) {
                        echo '✋ Could not activate addon ' . $addon . '!', PHP_EOL;
                        continue;
                    }
                }
                else {
                    echo '✋ Addon ' . $addon . ' is already installed!', PHP_EOL;
                    continue;
                }
                echo '✅ Installed addon ' . $addon . '.', PHP_EOL;
            }


            // step 3/4
            // import database
            foreach ($config[$options['demo']]['dbimport'] as $import) {

                $file = rex_backup::getDir() . '/' . $import;
                $success = rex_backup::importDb($file);
                if (!$success['state']) {
                    echo '✋ Could not import from ' . $import . ': ' . $success['message'], PHP_EOL;
                    continue;
                }
                echo '✅ Imported data from ' . $import . '.', PHP_EOL;
            }


            // step 4/4
            // import files
            foreach ($config[$options['demo']]['fileimport'] as $import) {

                $file = rex_backup::getDir() . '/' . $import;
                $success = rex_backup::importFiles($file);
                if (!$success['state']) {
                    echo '✋ Could not import from ' . $import . ': ' . $success['message'], PHP_EOL;
                    continue;
                }
                echo '✅ Imported data from ' . $import . '.', PHP_EOL;
            }

            // finish
            echo '🚀 ' . $options['demo'] . ' setup successful.', PHP_EOL;
            exit(0);
        }

        echo '✋ No config available!', PHP_EOL;
        exit(1);
    }

    echo '✋ REDAXO not yet installed! Skip installing ' . $options['demo'] . '.', PHP_EOL;
    exit(1);
}
