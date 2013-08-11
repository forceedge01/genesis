<?php

require_once __DIR__ . '/../Loader.php';
require_once __DIR__ . '/../Core/Lib/Set.Class.php';
require_once __DIR__ . '/../Core/Lib/Get.Class.php';
require_once __DIR__ . '/Config/Console.Config.php';

if(!\Get::Config('Console.ALLOW_CONSOLE_FROM_BROWSER') and isset($_SERVER['SERVER_NAME']))
{
    header('Location: '.str_replace('Application/Console', '', $_SERVER['PHP_SELF'])); die();
}

require __DIR__ . '/Console.php';

function getOptions() {

    return array(
        'Bundles' => array(
            'bundle:create',
            'bundle:delete',
            'bundle:assets:create',
            'bundle:assets:delete'
        ),
        'Components' => array(
            'component:create',
            'component:delete'
        ),
        'Tests' => array(
            'test:routes',
            'test:classes',
            'test:methods',
            'test:templates',
            'test:model',
            'test:all',
        ),
        'Cache' => array(
            'cache:clear'
        ),
        'Other' => array(
            'automate:testing-(beta)',
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

requireAll(\Get::Config('Console.LIB_FOLDER'));

$console = new Application\Console\Console();

$console->init();