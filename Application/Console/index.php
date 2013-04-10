<?php

require_once __DIR__ . '/../Configs/ConsoleDirs.php';

function requireAll($directory) {

    $files = scandir($directory);

    foreach ($files as $file) {

        if ($file != '.' && $file != '..' && $file != 'index.php') {

            if (is_file(CONSOLE_LIB_FOLDER . '/' . $file))
                require_once CONSOLE_LIB_FOLDER . '/' . $file;
            else
                requireAll(CONSOLE_LIB_FOLDER . '/' . $file);
        }
    }
}

function getOptions(){

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

        $args[0] = explode(':', $args[0]);

        if ($args[0][0] == 'bundle') {

            $bundle = new Bundle('console');

            switch ($args[0][1]) {

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
        } else {

            if ($args[0][0] == 'exit')
                exit;

            $console->unknownOption();
        }
    }
}
else{

    echo '<h3>Welcome to Genesis CRUD generator, please choose an option and proceed with the onscreen instructions.</h3>';

        $options = getOptions();

        $bundle = new Bundle('html');

        if(isset($_POST['submitOption'])){

            $option = $_POST['option'];

            $args = explode(':', $option[0]);

            if ($args[0] == 'bundle') {

                $bundle->name = ($_POST['bundleName'][0] ? $_POST['bundleName'][0] : $_POST['bundle'] );

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

            } else {

                if ($args[0] == 'exit')
                    exit;

                echo 'Option not recognized';
            }

        }

        echo '<form method="post">';

        foreach($options as $option){

            echo '<input type="radio" name="option[]" value="'.$option.'">'.$option.'</input><br />';
        }

        echo '<h4>List of bundles installed</h4>';

        $bundles = $bundle->readBundles(true);

        foreach($bundles as $bundle){

            echo '<input type="radio" name="bundleName[]" value="'.$bundle.'">'.$bundle.'</input><br />';
        }

        echo '<br />New Bundle Name: <input type="text" name="bundle"><br /><br />';

        echo '<input type="submit" name="submitOption"></form>';

}