<?php

require_once __DIR__ . '/../Resources/Configs/Core/BundleGenerator.php';

if(!ALLOW_BUNDLE_CREATION_FROM_BROWSER && isset($_SERVER['HTTP_HOST'])){
    
    echo 'Access restricted.';
    exit;
}

function getOptions() {

    return array(
        'bundle:create',
        'bundle:delete',
        'exit'
    );
}

function requireAll($directory) {

    $files = scandir($directory);

    $files = array_reverse($files);

    foreach ($files as $file) {

        if ($file != '.' && $file != '..' && $file != 'index.php') {

            if (is_file(CONSOLE_LIB_FOLDER . '/' . $file))
                require_once CONSOLE_LIB_FOLDER . '/' . $file;
            else
                requireAll(CONSOLE_LIB_FOLDER . '/' . $file);
        }
    }
}

requireAll(CONSOLE_LIB_FOLDER);

$console = new Console();

$console->init();