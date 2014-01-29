<?php

require __DIR__ . '/../AppKernal.php';
require __DIR__ . '/../Core/Lib/Set.Class.php';
require __DIR__ . '/../Core/Lib/Get.Class.php';
require __DIR__ . '/Config/Console.Config.php';

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
            'bundle:assets:delete',
            'bundle:verify'
        ),
        'Components' => array(
            'component:list',
            'component:create',
            'component:delete'
        ),
        'Schema' => array(
            'schema:export[:{database}::'.Get::Config('Database.name').']',
            'schema:import:{file}',
            'schema:drop[:{database}::'.Get::Config('Database.name').']',
            'schema:execute:{query}'
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
            'help',
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