<?php

require_once __DIR__ . '/../../Core/Config/AppDirs.Config.php';

Set::Config('Console', array(
    'ROOT' => \Get::Config('APPDIRS.APPLICATION_FOLDER') . 'Console',
    'LIB_FOLDER' => \Get::Config('APPDIRS.APPLICATION_FOLDER') . 'Console/Lib',
    'ALLOW_CONSOLE_FROM_BROWSER' => true
));

Set::OverwriteConfig('APPDIRS.BUNDLES.ASSETS_FOLDER', ROOT . 'Public/Assets/Bundles/');