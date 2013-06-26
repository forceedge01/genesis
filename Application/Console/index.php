<?php

require __DIR__ . '/../Resources/Configs/Core/BundleGenerator.Config.php';

require __DIR__ . '/Console.php';

function getOptions() {

    return array(
        'Bundles' => array(
            'bundle:create',
            'bundle:delete',
        ),
        'Tests' => array(
            'test:routes',
            'test:classes',
            'test:methods',
            'test:templates',
            'test:model',
            'test:all',
        ),
        'Other' => array(
            'automate:testing',
            'exit'
        )
    );
}

function requireAll($directory) {

    $files = scandir($directory);

    if(is_array($files))
    {
        foreach ($files as $file) {

            if ($file != '.' && $file != '..' && $file != 'index.php') {

                if (is_file($directory . '/' . $file))
                {
                    require_once $directory . '/' . $file;
                }
                else
                {
                    requireAll($directory . '/' . $file);
                }
            }
        }
    }
    else
        echo 'Failed retrieving files from '.$files;
}

requireAll(CONSOLE_LIB_FOLDER);

$console = new Application\Console\Console();

$console->init();