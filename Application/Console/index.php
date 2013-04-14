<?php

require_once __DIR__ . '/../Configs/ConsoleDirs.php';

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

function getOptions() {

    return array(
        'bundle:create',
        'bundle:delete',
        'exit'
    );
}

requireAll(CONSOLE_LIB_FOLDER);

if (!isset($_SERVER['SERVER_NAME'])) {
    $console = new Console();

    echo 'Welcome to Genesis console generator, please choose an option and proceed with the onscreen instructions.';
    $console->linebreak(1);

    while (true) {

        $console->showAllOptions(getOptions());

        echo 'Enter choice: ';

        $line = $console->readUser();

        $args = explode('--', $line);

        switchOption($args[0]);
    }
} else {

    echo '<h3>Welcome to Genesis CRUD generator, please choose an option and proceed with the onscreen instructions.</h3>';

    $options = getOptions();

    $bundle = new Bundle('html');

    if (isset($_POST['submitOption'])) {

        $option = $_POST['option'];

        switchOption($option[0]);
    }

    echo '<form method="post">';

    foreach ($options as $option) {

        echo '<input type="radio" name="option[]" value="' . $option . '">' . $option . '</input><br />';
    }

    echo '<h4>List of bundles installed</h4>';

    $bundles = $bundle->readBundles(true);

    foreach ($bundles as $bundle) {

        echo '<input type="radio" name="bundleName[]" value="' . $bundle . '">' . $bundle . '</input><br />';
    }

    echo '<br />New Bundle Name: <input type="text" name="bundle"><br /><br />';

    echo '<input type="submit" name="submitOption"></form>';
}

function switchOption($switch) {

    $args = explode(':', $switch);

    if ($args[0] == 'bundle') {

        if (isset($_SERVER['SERVER_NAME'])) {

            $bundle = new Bundle('html');

            $bundle->name = ($_POST['bundle'] ? $_POST['bundle'] : $_POST['bundleName'][0] );

        } else {

            $bundle = new Bundle('console');
        }
    }

    switch (strtolower($args[0])) {

        case 'bundle':

            switch ($args[1]) {

                case 'create':
                    $bundle->createBundle();
                    break;
                case 'delete':
                    $bundle->deleteBundle();
                    break;
                case '0':
                case 'exit':
                    exit(0);
                    break;
            }
            break;

        default:
            echo 'Exiting';
            exit;
            break;
    }
}