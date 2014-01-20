<?php

namespace Application\Core;

require_once __DIR__ . '/Core/Interfaces/Debugger.Interface.php';
require_once __DIR__ . '/Core/Lib/Debugger.Class.php';
require_once __DIR__ . '/Loader.php';
require_once __DIR__ . '/Core/Lib/Set.Class.php';
require_once __DIR__ . '/Core/Config/AppDirs.Config.php';
require_once __DIR__ . '/Core/Lib/Get.Class.php';

class AppKernal extends Loader{

    public static
            $phpVersion,
            $msyqlVersion,
            $scriptStartTime;

    public static function GetHost()
    {
        return "http://{$_SERVER['HTTP_HOST']}/";
    }

    /**
     * Initializes an instance of the application
     */
    public static function Initialize() {

        self::$scriptStartTime = microtime(true);
        self::CheckDependencies();
        self::LoadFramework();

        $app = new Application();

        if(!$app->ForwardRequest())
        {
            $app->ForwardToController('404', array('pattern'=> $app->GetPattern()));
        }
    }

    private static function CheckDependencies(){

        $version = '5.3.0';

        if(!version_compare(phpversion(), $version, '>='))
            die('You need to update your php version, GENESIS requires atleast php '.$version);
    }

    /**
     *
     * @param type $fileType
     * @return type
     * Gets info on file types loaded
     */
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

//spl_autoload_register(__NAMESPACE__ . '\Loader::LoadClass');