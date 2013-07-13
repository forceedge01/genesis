<?php

require_once __DIR__ . '/../Core/AppDirs.Config.php';

\Set::Config('Console', array(
    'ROOT' => \Get::Config('CORE.APPLICATION_FOLDER') . 'Console',
    'LIB_FOLDER' => \Get::Config('CORE.APPLICATION_FOLDER') . 'Console/Lib',
    'ALLOW_CONSOLE_FROM_BROWSER' => true
));