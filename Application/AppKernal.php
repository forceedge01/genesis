<?php

namespace Application;

use Application\Core\Lib\Debugger;

require __DIR__ . '/Core/Interfaces/DebuggerInterface.php';
require __DIR__ . '/Core/Lib/Debugger.php';

class AppKernal {

    private static $loader, $app;
    public static $scriptStartTime;

    public static function ComponentsRegister()
    {
        \Set::Component('Session', 'Application\Components\Session\SessionHandler');
        \Set::Component('HTMLGenerator', 'Application\Components\HTMLGenerator\HTMLGenerator');
        \Set::Component('Auth', 'Application\Components\Vault\Auth');
        \Set::Component('DatabaseManager', 'DatabaseManager\Lib\DatabaseManager');
        \Set::Component('DBConnection', 'DatabaseManager\Lib\Database');
        \Set::Component('Router', '\Application\Components\Router\Router');
        \Set::Component('Template', '\Application\Components\TemplateHandler\TemplateHandler');
        \Set::Component('Request', '\Application\Core\Lib\Request');
        \Set::Component('DependencyInjector', '\Application\Components\DependencyInjection\DependencyInjector');
        \Set::Component('EventDispatcher', '\Application\Components\EventDispatcher\EventDispatcher');
        \Set::Component('EventHandler', '\Application\Components\EventDispatcher\EventHandler');
        \Set::Component('Response', '\Application\Components\Response\Response');
    }

    public static function GenesisDependencies()
    {
        // config and interface to implement
        return array(
            'routeHandler' => 'Application\Core\Interfaces\RouterInterface',
            'templateHandler' => 'Application\Core\Interfaces\TemplateInterface',
            'requestHandler' => 'Application\Core\Interfaces\RequestInterface'
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

        Debugger::debugMessage('Registering components register');
        self::ComponentsRegister();
    }

    public static function getLoader() {

        Debugger::debugMessage('Initiated fetching of loader object');

        if(self::$loader) {
            Debugger::debugMessage('Loader pre initialized, returning pre-feted');

            return self::$loader;
        }

        require_once __DIR__ . '/Loader.php';

        self::$loader = new \Application\Loader();

        Debugger::debugMessage('Fetched loader');

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