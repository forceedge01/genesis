<?php

namespace Application\Core;



use \Application\Core\Interfaces\Debugger as DebuggerInterface;


abstract class Debugger implements DebuggerInterface{

    private $message, $file, $line, $errorNumber;

    private function __construct()
    {
        // Set error reporting on
        ini_set('error_reporting', E_ALL);

        // Enable garbage collector
        gc_enable();
    }

    /**
     *
     * @param void $params any variable to output to the screen
     */
    public static function pre()
    {
        $params = func_get_args();
        $callers = debug_backtrace();

        foreach($params as $param)
        {
            self::preOut($param);
            echo '<b>Output from</b>: '.get_called_class().' :: '.$callers[1]['function'].'(), <b>Line:</b> '.$callers[0]['line'];
        }
    }

    private static function preOut($param)
    {
        echo '<pre>';
        print_r($param);
        echo '</pre>';
    }

    /**
     *
     * @param void $params any variable to output to the screen and then stop execution of the program
     */
    public static function prex()
    {
        $params = func_get_args();
        $callers = debug_backtrace();

        foreach($params as $param)
        {
            self::preOut($param);
            echo '<b>Output from</b>: '.get_called_class().' :: '.$callers[1]['function'].'(), <b>Line:</b> '.$callers[0]['line'];
        }

        exit;
    }

    public static function prexPOST()
    {
        self::prex($_POST);
    }

    public static function prexGET()
    {
        self::prex($_GET);
    }

    public static function prexSERVER()
    {
        self::prex($_SERVER);
    }

    public static function prexSESSION()
    {
        self::prex($_SESSION);
    }

    public static function prexCOOKIE()
    {
        self::prex($_COOKIE);
    }

    /**
     * Display debugging information
     */
    public function debug() {

        self::pre($this);
        echo '<br />Libs loaded', print_r(AppKernal::get(), true), '<br />Debug data: <br /><br />';
        self::pre(debug_backtrace());
    }

    /**
     * @param int $errorNumber
     * @param String $this -> message
     * @param String __FILE__
     * @param int __LINE__
     * @param int $warning
     * @desc trigger an error with line number
     */
    public function ThrowError($warning = E_USER_ERROR) {

        if ($this->message) {

            echo '<title>Error: ' . $this->message . '</title>';

            echo '
        <style>

        #errorWrapper
        {
            margin: 0px auto;
            position: relative;
            width: 80%;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid gray;
            background: whitesmoke;
            line-height: 25px;
            font-size: 12px;
            font-weight: bold;
            font-family: verdana;
        }

        #errorHeader
        {
            height: 50px;
            font-size: 18px;
        }

        #errorWrapper #errorMessage
        {
            float: left;
            width: 85%;
        }

        #errorWrapper #errorNo
        {
            float: right;
            height: 50px;
            font-size: 10px;
        }

        #errorLocation
        {
            padding-bottom: 20px;
            font-size: 15px;
        }

        #errorBacktrace
        {
        }

        </style>
        ';

            $debug = debug_backtrace();

            echo '<div id="errorWrapper">
                <div id="errorHeader">
                    <div id="errorMessage">Error Explanation: ', $this->message, '</div>
                    <div id="errorNo">Error No:', $this->errorNumber, '</div>
                </div>
                <hr>
                <div id="errorLocation">in ', $this->file, ' on Line: ', $this->line . '</div>

                ';

            echo '<div id="errorBacktrace">
                Backtrace:
                <pre>' . print_r($debug, true) . '</pre>
              </div>
            </div>';

            if (\Get::Config('Errors.mailTriggeredErrors'))
            {
                Loader::LoadComponent('Mailer');
                $mail = new \Application\Components\Mailer();

                $mail->send(array(
                    'to' => \Get::Config('Application.Admin_Email'),
                    'from' => \Get::Config('Errors.errorsEmailAddress'),
                    'subject' => $this->errorNumber . ' in ' . $this->file . ' on line: ' . $this->line,
                    'message' => 'Error No:' . $this->errorNumber . ' Explanation:' . $this->message . ' in ' . $this->file . ' on line: ' . $this->line . ' Dated: ' . date('l, d F, Y H:i:s'),
                ));
            }

            trigger_error('Error No: ' . $this->errorNumber . ' Explanation: ' . $this->message . ' in ' . $this->file . ' on line: ' . $this->line . ' Dated: ' . date('l, d F, Y H:i:s'), $warning);
        }
    }

    public function ThrowException() {

        $message = new \Exception($this->message);

        echo '<title>Error: ' . $this->message . '</title>';

        echo '
        <style>

        #errorWrapper
        {
            margin: 0px auto;
            position: relative;
            width: 80%;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid gray;
            background: whitesmoke;
            line-height: 25px;
            font-size: 12px;
            font-weight: bold;
            font-family: verdana;
        }

        #errorHeader
        {
            height: 50px;
            font-size: 18px;
        }

        #errorWrapper #errorMessage
        {
            float: left;
            width: 85%;
        }

        #errorWrapper #errorNo
        {
            float: right;
            height: 50px;
            font-size: 10px;
        }

        #errorLocation
        {
            padding-bottom: 20px;
            font-size: 15px;
        }

        #errorBacktrace
        {
        }

        </style>
        ';

        echo '<div id="errorWrapper">
                <div id="errorHeader">
                    <div id="errorMessage">Caught Exception: ', $this->message, '</div>
                </div>
                <hr>
                <div id="errorLocation">in ', $this->file, ' on Line: ', $this->line . '</div>
                ';

        echo '<div id="errorBacktrace">
                Backtrace:
                <pre>' . $message . '</pre>
              </div>
            </div>';

        if (\Get::Config('Errors.mailTriggeredErrors'))
        {
            Loader::LoadComponent('Mailer');
            $mail = new \Application\Components\Mailer();

            $mail->send(array(
                'to' => \Get::Config('Application.Admin_Email'),
                'from' => \Get::Config('Errors.mailTriggeredErrors'),
                'subject' => $this->message . ' in ' . $this->file . ' on line: ' . $this->line,
                'message' => 'Caught Exception:' . $this->message . ' in ' . $this->file . ' on line: ' . $this->line . ' Dated: ' . date('l, d F, Y H:i:s') . $this->message,
            ));
        }

        exit;
    }

    /**
     *
     * @param type $message
     * @param type $file
     * @param type $line
     * @param type $errorNumber
     * @return \Application\Core\Debugger
     */
    public function SetErrorArgs($message, $file, $line, $errorNumber = 0)
    {
        $this->message = $message;
        $this->file = ($file) ? $file : get_called_class();
        $this->line = $line;
        $this->errorNumber = $errorNumber;

        return $this;
    }

    public static function ThrowStaticError($message, $file = __FILE__, $line = __LINE__)
    {
        echo '
        <style>

        #errorWrapper
        {
            margin: 0px auto;
            position: relative;
            width: 80%;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid gray;
            background: whitesmoke;
            line-height: 25px;
            font-size: 12px;
            font-weight: bold;
            font-family: verdana;
        }

        #errorHeader
        {
            height: 50px;
            font-size: 18px;
        }

        #errorWrapper #errorMessage
        {
            float: left;
            width: 85%;
        }

        #errorWrapper #errorNo
        {
            float: right;
            height: 50px;
            font-size: 10px;
        }

        #errorLocation
        {
            padding-bottom: 20px;
            font-size: 15px;
        }

        #errorBacktrace
        {
        }

        </style>
        ';

        echo '<div id="errorWrapper">
            <div id="errorHeader">
                <div id="errorMessage">Error Explanation: ', $message, '</div>
            </div>
            <hr>
            <div id="errorLocation">in ', $file, ' on Line: ', $line . '</div>

            ';
    }
}