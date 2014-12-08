<?php

namespace Application;

require __DIR__ . '/Core/Interfaces/Debugger.Interface.php';
require __DIR__ . '/Core/Lib/Debugger.Class.php';

use Application\Core\Debugger;

class AppKernal {

    private static $loader, $app;
    public static $scriptStartTime;

    public static function ComponentsRegister()
    {
        \Set::Component('Session', 'Session\SessionHandler');
        \Set::Component('HTMLGenerator', 'HTMLGenerator\HTMLGenerator');
        \Set::Component('Router', 'Router\Router');
        \Set::Component('Auth', 'Auth\Auth');
        \Set::Component('TemplateHandler', 'TemplateHandler\TemplateHandler');
    }

    public static function BundlesRegister()
    {

    }

    public static function GenesisDependencies()
    {
        return array(
            'Router' => 'Application\Core\Interfaces\Router',
            'TemplateHandler' => 'Application\Core\Interfaces\Template'
        );
    }

    public static function GetHost()
    {
        if(isset($_SERVER['HTTP_HOST']))
        {
            $http = 'http';

            if(isset($_SERVER['HTTPS']))
            {
                $http = 'https';
            }

            return "{$http}://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";
        }
    }

    /**
     * Initializes an instance of the application
     */
    public static function Initialize() {

        Core\Debugger::debugMessage('Registering components register');

        self::ComponentsRegister();

        Core\Debugger::debugMessage('Registering bundles register');

        self::BundlesRegister();
    }

    public static function getLoader() {

        Core\Debugger::debugMessage('Initiated fetching of loader object');

        if(self::$loader)
        {
            Core\Debugger::debugMessage('Loader pre initialized, returning pre-feted');

            return self::$loader;
        }

        require_once __DIR__ . '/Loader.php';

        self::$loader = new \Application\Loader();

        Core\Debugger::debugMessage('Fetched loader');

        return self::$loader;
    }

    /**
     *
     * @param string Append text
     * @return mixed Returns execution time in Milliseconds
     */
    public static function GetExecutionTime($text = 'Milliseconds')
    {
        if($text)
            $text = ' '.$text;

        return round(((microtime(true) - self::$scriptStartTime)), 5).$text;
    }
}