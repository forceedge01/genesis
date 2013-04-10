<?php

require_once __DIR__ . '/../Configs/ConsoleDirs.php';

function requireAll($directory){

    $files = scandir($directory);

    foreach($files as $file){

        if($file != '.' && $file != '..' && $file != 'index.php'){

            if(is_file(CONSOLE_LIB_FOLDER . '/' .$file))
                require_once CONSOLE_LIB_FOLDER . '/' . $file;
            else
                requireAll (CONSOLE_LIB_FOLDER . '/' .$file);
        }
    }
}

requireAll(CONSOLE_LIB_FOLDER);

$console  = new Console();

echo 'Welcome to Genesis console generator, please choose an option and proceed with the onscreen instructions.';
$console->linebreak(1);

while(true){

    $console->showAllOptions();

    echo 'Enter choice: ';

    $line = $console->readUser();

    $args = explode('--', $line);

    $args[0]= explode(':', $args[0]);

    if($args[0][0] == 'bundle'){

        $bundle = new Bundle();

        switch($args[0][1]){

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
    }
    else{

        if($args[0][0] == 'exit')
            exit;

        $console->unknownOption();

    }

}