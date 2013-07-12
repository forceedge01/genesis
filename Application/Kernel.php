<?php

namespace Application\Core;



require_once __DIR__ . '/Resources/Configs/Core/AppDirs.Config.php';
require_once __DIR__ . '/Loader.php';

class AppKernal extends Loader{

    public static
            $phpVersion,
            $msyqlVersion,
            $scriptStartTime,
            $env;

    public static function Initialize($env) {

        self::$env = $env;
        self::$scriptStartTime = microtime();
        self::CheckDependencies();
        self::LoadFramework(self::$env);
        $route = new Router();

        if(!$route->ForwardRequest()){

            header( 'HTTP/1.1 404 Not Found', true, 404 );
            die('<h4>Pattern: ' . $route->GetPattern() . ' Not Found, <a href="javascript:history.back();">go back to last page</a>.</h4>');
        }
    }

    private static function CheckDependencies(){

        $version = '5.3.0';

        if(version_compare(phpversion(), $version, '>='))
            self::$phpVersion = phpversion();
        else
            die('You need to update your php version, GENESIS needs atleast php '.$version);
    }

    public static function Get($fileType = null){

        if(emtpy($fileType))
            return array(
                'Interfaces' => self::$interfaces,
                'Traits' => self::$traits,
                'Bundles' => self::$bundles,
                'Classes' => self::$classes,
                'Components' => self::$components,
                'Configs' => self::$configs,
                'Controllers' => self::$controllers,
                'Files' => self::$files,
                'Models' => self::$models,
                'Routes' => self::$routes
            );

        return self::$$fileType;
    }
}

AppKernal::Initialize('development');