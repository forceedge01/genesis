<?php

namespace Application\Core;



require_once __DIR__ . '/Resources/Configs/Core/AppDirs.Config.php';
require_once __DIR__ . '/Loader.php';

class AppKernal extends Loader{

    public static
            $phpVersion,
            $msyqlVersion,
            $scriptStartTime;

    public static function initialize() {

        self::$scriptStartTime = microtime();

        self::checkDependencies();

        self::loadFramework();

        $route = new Router();

        if(!$route->forwardRequest()){

            header( 'HTTP/1.1 404 Not Found', true, 404 );

            echo '<h1>Pattern: ' . $route->GetPattern() . ' Not Found!!</h1>';

            exit;
        }
    }

    private static function checkDependencies(){

        $version = '5.3.0';

        if(version_compare(phpversion(), $version, '>='))
                self::$phpVersion = phpversion();
        else{
            echo 'You need to update your php version, GENESIS needs atleast php '.$version;
            exit;
        }
    }

    public static function get($fileType = null){

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

AppKernal::initialize();