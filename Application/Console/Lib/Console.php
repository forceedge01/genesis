<?php

class Console {

    public function init(){

        if (!isset($_SERVER['SERVER_NAME'])) {

            echo 'Welcome to Genesis console generator, please choose an option and proceed with the onscreen instructions.';
            $this->linebreak(1);

            while (true) {

                $this->showAllOptions(getOptions());

                echo 'Enter choice: ';

                $line = $this->readUser();

                $args = explode('--', $line);

                $this->switchOption($args[0]);
            }
        } else {

            echo '<h3>Welcome to Genesis CRUD generator, please choose an option and proceed with the onscreen instructions.</h3>';

            $options = getOptions();

            $bundle = new Bundle('html');

            if (isset($_POST['submitOption'])) {

                $option = $_POST['option'];

                $this->switchOption($option[0]);
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
    }

    public function unknownOption() {

        echo 'Unknown option!';

        $this->showAllOptions();

        $message = 'Enter option: ';

        $option = $this->readUser($message);

        return $option;
    }

    public function readUser($message = null) {

        if (!empty($message))
            echo $message;

        if (!isset($_SERVER['SERVER_NAME'])) {

            $handle = fopen('php://stdin', 'r');

            $line = trim(fgets($handle));

            return $line;
        }

        return '';
    }

    public function writeUser($message) {

        echo $message;
    }

    public function showAllOptions($options) {

        $this->linebreak(2);

        foreach ($options as $option) {

            echo $option;
            $this->linebreak(1);
        }

        $this->linebreak(2);
    }

    protected function removeDirectory($directory) {

        try {

            if (is_dir($directory)) {

                $files = scandir($directory);

                foreach ($files as $file) {
                    if (($file != '.' && $file != '..')) {

                        $absolutePath = $directory . '/' . $file;

                        if (is_dir($absolutePath)) {
                            echo 'Entring direcotry';
                            $this->linebreak(1);
                            $this->removeDirectory($absolutePath);
                        } else {
                            echo 'deleting file: ' . $absolutePath;
                            $this->linebreak(1);
                            unlink($absolutePath);
                        }
                    }
                }

                if (rmdir($directory))
                    return true;
                else
                    return false;
            }
            else
                return false;
        } catch (Exception $e) {

            echo $e->getMessage();

            return false;
        }
    }

    public function linebreak($val) {

        for ($i = 0; $i < $val; $i++) {

            if (!isset($_SERVER['SERVER_NAME']))
                echo chr(10) . chr(13);
            else
                echo '<br />';
        }
    }

    function switchOption($switch) {

        $args = explode(':', $switch);

        if ($args[0] == 'bundle') {

            if (isset($_SERVER['SERVER_NAME'])) {

                $bundle = new Bundle('html');

                $bundle->name = str_replace('bundle', '', strtolower(($_POST['bundle'] ? $_POST['bundle'] : $_POST['bundleName'][0] )));

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

}