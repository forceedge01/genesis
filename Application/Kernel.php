<?php

require_once __DIR__ . '/Resources/Configs/AppDirs.php';

class AppKernal {

    private static
            $classes = array(),
            $configs = array(),
            $controllers = array(),
            $routes = array(),
            $entities = array(),
            $bundles = array(),
            $components = array(),
            $files = array();

    public static
            $phpVersion,
            $msyqlVersion;

    private static function fetchAllBundles(){

        // Include your bundles here

        $bundles = array(

            'Welcome',
            'testBundle',
        );

        // Do not edit below this line

        $bundlesDIR = BUNDLES_FOLDER;

        foreach($bundles as $bundle){

            self::$bundles[] = $bundlesDIR . $bundle;
        }

    }

    private static function fetchAllClasses($classDir){

        $classes = array(

            'Debugger.php',
            'AppMethods.php',
            'Request.php',
            'Router.php',
            'Template.php',
            'Application.php',
            'Database.php',
            'Auth.php',
            'Session.php',

        );

        foreach($classes as $class){

            if(is_file($classDir . $class))
                self::$classes[] = $classDir . $class;
            else
                echo '<h1>Class '.$classDir.$class.' not found in kernel::fetchAllClasses</h1>';
        }
        
        return self::$classes;
    }

    public static function initialize() {

        self::checkDependencies();

        self::load('configs', APPLICATION_CONFIGS_FOLDER);

        self::load('classes', APPLICATION_CLASSES_FOLDER);
        
        self::load('components', APPLICATION_COMPONENTS_FOLDER);

        self::load('routes', APPLICATION_ROUTES_FOLDER);

        self::load('entities', APPLICATION_ENTITIES_FOLDER);

        self::load('controllers', APPLICATION_CONTROLLERS_FOLDER);

        self::loadBundles();

        $route = new Router();

        if(!$route->forwardRequest()){

            echo '<h1>Pattern: ' . $route->pattern . ' Not Found!!</h1>';

            exit;
        }
    }

    private static function fetchAll($dir){

        $directory = $dir;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file) && self::fileExtensionIs($directory . $file, array('php')))
                self::$files[] = $directory .$file;
            else if($file != '.' && $file != '..' && is_dir($directory . $file))
                self::fetchAll ($directory . $file . '/');
        }
        
        return self::$files;
    }

    private static function load($staticVar, $dir){
        
        self::$files = array();

        if($staticVar == 'classes')
            self::$$staticVar = self::fetchAllClasses ($dir);
        else
            self::$$staticVar = self::fetchAll($dir);

        foreach(self::$$staticVar as $file)
            require_once $file;
    }

    private static function loadBundles(){

        self::fetchAllBundles();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                self::loadFilesFromDir($bundle . '/Resources/Configs', array('php'));
                self::loadFilesFromDir($bundle . '/Resources/Routes', array('php'));
                self::loadFilesFromDir($bundle, array('php'));
                self::loadFilesFromDir($bundle . '/Controllers', array('php'));
                self::loadFilesFromDir($bundle . '/Entities', array('php'));
            }
            else{

                $params['Backtrace'] = debug_backtrace();

                $message = ' not found in kernel::loadBundles()';

                require_once BUNDLES_FOLDER . 'Errors/Templates/ControllerViews/Bundle_Not_Found.html.php';

                trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);

                exit;

            }

        }

    }

    private static function loadFilesFromDir($directory, array $extensions){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = $directory . '/' . $file;

                if(is_file($filepath) && self::fileExtensionIs($filepath, $extensions))
                    require_once $filepath;
            }
        }

        return true;

    }

    private static function checkDependencies(){

        self::$phpVersion = phpversion();
    }
    
    private static function fileExtensionIs($file, array $extensions){
        
        $exists = false;
        
        foreach($extensions as $extensions){
            
            if(pathinfo($file, PATHINFO_EXTENSION) == $extensions){
                $exists = true;
                break;
            }
        }
        
        return $exists;
    }
    
    public static function show($fileType){
        
        foreach(self::$$fileType as $files){
            
            echo $files . '<br />';
        }
    }
}

AppKernal::initialize();