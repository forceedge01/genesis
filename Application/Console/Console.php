<?php

namespace Application\Console;



class Console {

    private
            $object;

    public function init(){

        if (!isset($_SERVER['SERVER_NAME'])) {

            echo $this ->blue(' =========>>> Welcome to Genesis Simplify Engine, please choose an option and proceed with the onscreen instructions. <<<=========') ;
            $this->linebreak(1);

            while (true) {

                if(isset($_SERVER['argv'][1]))
                {
                    $this->switchOption(str_replace('--', '', $_SERVER['argv'][1]));
                    exit;
                }

                $this->showAllOptions(getOptions());

                echo 'Enter choice: ';

                $line = $this->readUser();

                $args = explode('--', $line);

                $this->switchOption($args[0]);
            }
        } else {
            
            echo '
            <title>Simplify</title>
            <style>
            
            .mainHeading
            {
                color: steelblue; font-size: 18px; padding: 10px; background-color: whitesmoke;
            }
            
            .heading
            {
                color: steelblue; background-color: whitesmoke; padding: 10px; font-size: 16px; float: left; width: 100%; margin: 10px 0px;
            }
            
            .form
            {
                line-height: 25px;
            }
            
            .form input[type=text]
            {
                width: 200px;
                padding: 5px;
            }
            
            .form .option label:hover
            {
                background-color: ghostwhite;
            }
            
            .option input
            {
                float: left;
                width: 3%;
            }
            
            .option label
            {
                display: block;
                float: left;
                width: 96%;
                padding: 3px;
            }
            
            .subHeading
            {
                color: yellowgreen; cont-size: 14; padding: 5px;
            }
            input[type="radio"]:checked+label
            {
                color: orange;
            }
            
            </style>
            ';

            echo '<div class="mainHeading">Welcome to Genesis Simplify Engine.</div>';

            $options = getOptions();

            $bundle = new Libraries\Bundle('html');

            if (isset($_POST['submitOption'])) {

                $option = $_POST['option'];

                $this->switchOption($option[0]);
            }

            echo '<form method="post" class="form">';

            foreach ($options as $key => $option) {

                echo "<div class='subHeading'>$key</div>";
                
                foreach($option as $opt)
                {
                    echo "<div class='option'><input type='radio' ".($_POST['option'][0] == $opt ? 'checked=checked' : '')." id='$opt' name='option[]' value='$opt'> <label for='$opt'> $opt</label></div><br />";
                }
            }

            echo '<div class="heading">List of bundles installed</div>';

            $bundles = $bundle->readBundles(true);

            foreach ($bundles as $bundle) {

                echo "<div class='option'><input type='radio' id='$bundle' ".($_POST['bundleName'][0] == $bundle ? 'checked=checked' : '')." name='bundleName[]' value='$bundle'> <label for='$bundle'>$bundle</label></div><br />";
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

        foreach ($options as $key => $option) {

            echo $this->green($key).$this->linebreak(1);

            foreach($option as $opt)
                echo $this->linebreak(1).' '.$opt;
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

    public function linebreak($val) 
    {
        for ($i = 0; $i < $val; $i++) {

            if (!isset($_SERVER['SERVER_NAME']))
                echo chr(10) . chr(13);
            else
                echo '<br />';
        }
    }
    
    public function space($num)
    {
        $space = null;
        
        for ($i = 0; $i < $num; $i++) {

            if (!isset($_SERVER['SERVER_NAME']))
                $space .= ' ';
            else
                $space .= '&nbsp;';
        }
        
        return $space;
    }

    function switchOption($switch) {

        $args = explode(':', $switch);

        switch (strtolower($args[0]))
        {
            case 'bundle':
            {
                if(!ALLOW_BUNDLE_CREATION_FROM_BROWSER AND isset($_SERVER['HTTP_HOST']))
                {
                    echo 'Access restricted.';
                    exit;
                }

                if (isset($_SERVER['SERVER_NAME']))
                {
                    $bundle = new Libraries\Bundle('html');
                    $bundle->name = str_replace('bundle', '', strtolower(($_POST['bundle'] ? $_POST['bundle'] : $_POST['bundleName'][0] )));
                }
                else
                {
                    $bundle = new Libraries\Bundle('console');
                }

                switch ($args[1])
                {
                    case 'create':
                    {
                        $bundle->createBundle();
                        break;
                    }
                    case 'delete':
                    {
                        $bundle->deleteBundle();
                        break;
                    }
                    case '0':
                    case 'exit':
                    {
                        exit(0);
                        break;
                    }
                }
                break;
            }

            case 'test':
            {
                $this->object = New Test();
                
                switch($args[1])
                {
                    case 'routes':
                    {
                        $this->object->RunTests('route');
                        break;
                    }

                    case 'classes':
                    {
                        $this->object->RunTests('class');
                        break;
                    }

                    case 'methods':
                    {
                        $this->object->RunTests('method');
                        break;
                    }

                    case 'templates':
                    {
                        $this->object->RunTests('template');
                        break;
                    }

                    case 'models':
                    {
                        $this->object->RunTests('model');
                        break;
                    }

                    case 'all':
                    {
                        if(!is_object($this->object))
                            $this->object = new Test();

                        $this->object->RunTests();

                        $this->object->clearResults();
                        break;
                    }
                }
                break;
            }
            
            case 'cache':
            {
                require_once APPLICATION_CLASSES_FOLDER . 'Core/Debugger.Class.php';
                require_once APPLICATION_COMPONENTS_FOLDER . 'Directory/Directory.Class.php';
                
                $cache = new Cache();
                switch($args[1])
                {
                    case 'clear':
                    {
                        $cache->Clear();
                        break;
                    }
                }
                break;
            }

            case 'automate':
            {
                switch($args[1])
                {
                    case 'testing':
                    {
                        $watcher = new Watcher($args[2], 'test:all');
                        $watcher ->automate();
                        break;
                    }
                }
                break;
            }

            default:
            {
                echo 'Exiting';
                exit;
                break;
            }
        }
    }

    public function green($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[32m".$string."\033[37m";
        else
            return "<font color='#339933'>$string</font>";
    }

    public function red($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[31m".$string."\033[37m";
        else
            return "<font color='#B80000'>$string</font>";
    }

    public function blackOnRed($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[41m".$string."\033[0m";
        else
            return "<div style='background-color: #CC9999; padding: 10px'>$string</div>";
    }

    public function greenOnRed($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[42m".$string."\033[0m";
        else
            return "<div style='background-color: #CCFF99; padding: 10px'>$string</div>";
    }

    public function blue($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[34m".$string."\033[37m";
        else
            return "<font color='steelblue'>$string</font>";
    }

    public function createFile($filePath, $content)
    {
        $handle = fopen($filePath, 'w+');
        fwrite($handle, $content);
        fclose($handle);
    }

    public function decide($readMessage, $htmlDefault)
    {
        return ( !$_SERVER['SERVER_NAME'] ? $this->readUser($readMessage) : $htmlDefault);
    }
}