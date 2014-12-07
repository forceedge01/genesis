<?php

namespace Application\Console;



use Application\Core\Debugger;

class Console extends Debugger {

    private
            $object;

    public function __construct() {}

    protected function persistOptions()
    {
        while (true)
        {
            if(isset($_SERVER['argv'][1]))
            {
                $this->switchOption(str_replace('--', '', $_SERVER['argv'][1]));
                exit;
            }

            $this->showAllOptions(getOptions());
            $line = $this->readUser('Enter choice: ');
            $args = explode('--', $line);
            $this->switchOption($args[0]);
        }
    }

    public function init()
    {
        if (!isset($_SERVER['SERVER_NAME']))
        {
            echo $this->AddBreaks($this->blue('=========>>> Welcome to Genesis Simplify Engine, please choose an option and proceed with the onscreen instructions. <<<========='));

            $this->persistOptions();
        }
        else
        {

            $title = '<title>Simplify</title>';
            $heading = '<div class="mainHeading">Welcome to Genesis Simplify Engine.</div>';
            $style = '<style>

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

            </style>';

            echo $title, $style, $heading;
            $options = getOptions();
            $bundle = new Libraries\Bundle('html');

            if (isset($_POST['submitOption']))
            {
                $option = $_POST['option'];
                $this->switchOption($option[0]);
            }

            echo '<form method="post" class="form">';
            foreach ($options as $key => $option)
            {
                echo "<div class='subHeading'>$key</div>";

                foreach($option as $opt)
                {
                    echo "<div class='option'><input type='radio' ".($_POST['option'][0] == $opt ? 'checked=checked' : '')." id='$opt' name='option[]' value='$opt'> <label for='$opt'> $opt</label></div><br />";
                }
            }

            echo '<div class="heading">List of bundles installed</div>';
            $bundles = $bundle->readBundles(true);

            foreach ($bundles as $bundle)
            {
                echo "<div class='option'><input type='radio' id='$bundle' ".($_POST['bundleName'][0] == $bundle ? 'checked=checked' : '')." name='bundleName[]' value='$bundle'> <label for='$bundle'>$bundle</label></div><br />";
            }

            echo '<br />New Bundle Name: <input type="text" name="bundle"><br /><br /><input type="submit" name="submitOption"></form>';
        }
    }

    public function unknownOption() {

        echo 'Unknown option!';

        return $this
                ->showAllOptions()
                    ->readUser('Enter option: ');
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

        foreach ($options as $key => $option)
        {
            echo $this->linebreak(1), $this->green($key);

            foreach($option as $opt)
            {
                echo $this->linebreak(1).' '.$opt;
            }
        }

        echo $this->linebreak(2);

        return $this;
    }

    protected function removeDirectory($directory) {

        try {

            if (is_dir($directory))
            {
                $files = scandir($directory);

                foreach ($files as $file)
                {
                    if (($file != '.' && $file != '..')) {

                        $absolutePath = $directory . '/' . $file;

                        if (is_dir($absolutePath))
                        {
                            echo $this->linebreak(), '.. ';
                            if(!$this->removeDirectory($absolutePath))
                                return false;
                        }
                        else
                        {
                            echo '. ';
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

        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    public static function linebreak($val = 1)
    {
        $linebreak = $break = null;

        if (!isset($_SERVER['SERVER_NAME']))
            $break = chr(10) . chr(13);
        else
            $break = '<br />';

        for ($i = 0; $i < $val; $i++)
        {
            $linebreak .= $break;
        }

        return $linebreak;
    }

    public static function space($num)
    {
        $spaces = $space = null;

        if (!isset($_SERVER['SERVER_NAME']))
            $space = ' ';
        else
            $space = '&nbsp;';

        for ($i = 0; $i < $num; $i++)
        {
            $spaces .= $space;
        }

        return $spaces;
    }

    function switchOption($switch) {

        require_once __DIR__ . '/initializer.php';

        Initializer::init($switch);

        $this->flushOptions();
        $this->persistOptions();
    }

    protected function flushOptions()
    {
        unset($_SERVER['argv'][1]);
    }

    public static function green($string)
    {
        if(Lib\Test::$output != 'Failures')
        {
            if (!isset($_SERVER['SERVER_NAME']))
                return "\033[32m".$string."\033[37m";
            else
                return "<font color='#339933'>$string</font>";
        }
    }

    public static function red($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[31m".$string."\033[37m";
        else
            return "<font color='#B80000'>$string</font>";
    }

    public static function blackOnRed($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[41m".$string."\033[0m";
        else
            return "<div style='background-color: #CC9999; padding: 10px'>$string</div>";
    }

    public static function greenOnRed($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[42m".$string."\033[0m";
        else
            return "<div style='background-color: #CCFF99; padding: 10px'>$string</div>";
    }

    public static function blue($string)
    {
        if (!isset($_SERVER['SERVER_NAME']))
            return "\033[34m".$string."\033[37m";
        else
            return "<font color='steelblue'>$string</font>";
    }

    public function createFile($filePath, $content)
    {
        try
        {
            $handle = fopen($filePath, 'w+');
            fwrite($handle, $content);
            fclose($handle);

            return true;
        }
        catch(Exception $e)
        {
            trigger_error($e->getMessage());
            return false;
        }
    }

    /**
     *
     * @param type $readMessage
     * @param type $htmlDefault
     * @return type
     */
    public function decide($readMessage, $htmlDefault)
    {
        return ( !isset($_SERVER['SERVER_NAME']) ? $this->readUser($readMessage) : $htmlDefault);
    }

    /**
     *
     * @param type $string
     * @param type $breaks
     * @return type
     */
    public function AddBreaks($string, $breaks = 1)
    {
        return $this->linebreak($breaks). $string. $this->linebreak($breaks);
    }

    private function is_dir_empty($dir) {

        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }

    public function Choice($message, $defaultHtml = 'yes')
    {
        $ans = $this->decide($this->blue($message.' [yes/no]: '), $defaultHtml);
        if($ans == 'yes')
            return true;
        else if($ans == 'no')
            return false;
        else
            return $this->Choice ($message, $defaultHtml);
    }

    public function ShowFormattedArray(array $array, $increment = 0)
    {
        foreach($array as $key => $value)
        {
            if($increment !== false)
            {
                if(is_numeric($key))
                    $key += $increment;

                echo '[ ',$this->green($key), ' ]:';
            }

            echo ' ', $value, $this->linebreak();
        }
    }

    public static function Legend()
    {
        echo ("\r\n".
        ' - [NUMBER] specifies an index option for you to choose from'.
        "\r\n".
        ' - [...] specifies an optional part of a command.'.
        "\r\n".
        ' - {...} specifies a variable to be replaced by a value by the user.'.
        "\r\n".
        ' - {...}::{value} shows the default value that is going to be used if the variable value is not provided.'.
        "\r\n".
        ' - Instructions will be given as you proceed with your choice'.
        "\r\n".
        ' - Example command: component:create OR schema:export:YourSchemaName'.
        "\r\n");
    }

    public static function HowToUse()
    {
        echo self::blue("\r\n".
        "You can execute commands by typing in an option from the menu or directory passing the option from the command line as an argument e.g\r\n".
        "$ Application/simplify --create:bundle would take you to the create bundle menu directly.\r\n"
        );
    }

    public function IsDirectoryEmpty($dir)
    {
        if(count(scandir($dir)) < 3)
            return $this;

        return false;
    }

    public function OutputMessages(array $messages)
    {
        foreach($messages as $key => $message)
        {
            foreach($message as $msg)
                echo $this->$key($msg), $this->linebreak();
        }
    }
}