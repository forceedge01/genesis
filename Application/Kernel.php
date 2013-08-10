<?php

namespace Application\Core;

require_once __DIR__ . '/Loader.php';
require_once __DIR__ . '/Core/Lib/Set.Class.php';
require_once __DIR__ . '/Core/Config/AppDirs.Config.php';
require_once __DIR__ . '/Core/Lib/Get.Class.php';

class AppKernal extends Loader{

    public static
            $phpVersion,
            $msyqlVersion,
            $scriptStartTime;

    public static function Initialize() {

        self::$scriptStartTime = microtime();
        self::CheckDependencies();
        self::LoadFramework();
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

spl_autoload_register(__NAMESPACE__ . '\Loader::LoadClass');

AppKernal::Initialize();