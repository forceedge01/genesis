<?php

namespace Application;


class AppKernal {

    private static $loader;

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

        $app = new Core\Application();

        if(!$app->ForwardRequest())
        {
            $app->ForwardToController('404', array('pattern'=> $app->GetPattern()));
        }
    }

    public static function getLoader() {

        if(self::$loader)
        {
            return self::$loader;
        }

        require_once __DIR__ . '/Loader.php';

        self::$loader = new Loader();

        return self::$loader;
    }
}