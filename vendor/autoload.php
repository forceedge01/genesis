<?php

// autoload.php generated by Composer

function fetchAllConfigs(){

    $directory = __DIR__ . '/../Application/Configs/';
    $files = scandir($directory);

    $configs = array();

    foreach($files as $file){

        if(is_file($directory . $file)){

            $configs[] = $directory .$file;
        }
    }

    foreach($configs as $config)
       require_once $config;
}

fetchAllConfigs();

require_once __DIR__ . '/composer' . '/autoload_real.php';

return ComposerAutoloaderInitab15c89b7b791f0356e01dd16e3485e8::getLoader();