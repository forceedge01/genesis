<?php

require_once __DIR__ . '/../../Core/Config/AppDirs.Config.php';

Set::Config('Console', array(
    'ROOT' => \Get::Config('APPDIRS.APPLICATION_FOLDER') . 'Console',
    'LIB_FOLDER' => \Get::Config('APPDIRS.APPLICATION_FOLDER') . 'Console/Lib',
    'CONFIG_FOLDER' => \Get::Config('APPDIRS.APPLICATION_FOLDER') . 'Console/Config/',
    'ALLOW_CONSOLE_FROM_BROWSER' => true,
    'APP_CONFIG_FOLDER' => __DIR__ . '/../../Core/Config/'
));

Set::OverwriteConfig('APPDIRS.BUNDLES.ASSETS_FOLDER', ROOT . 'Public/Assets/Bundles/');