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
            $components = array();

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

    private static function fetchAllClasses(){

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

        $classDir = APPLICATION_CLASSES_FOLDER;

        foreach($classes as $class){

            if(is_file($classDir . $class))
                self::$classes[] = $classDir . $class;
            else
                echo '<h1>Class '.$classDir.$class.' not found in kernel::fetchAllClasses</h1>';
        }
    }

    public static function initialize() {

        self::checkDependencies();

        self::loadConfigs();

        self::loadClasses();
        
        self::loadComponents();

        self::loadRoutes();

        self::loadEntities();

        self::loadControllers();

        self::loadBundles();

        $route = new Router();

        if(!$route->forwardRequest()){

            echo '<h1>Pattern: ' . $route->pattern . ' Not Found!!</h1>';

            exit;
        }
    }

    private static function fetchAllConfigs(){

        $directory = APPLICATION_CONFIGS_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                self::$configs[] = $directory .$file;
            }
        }
    }

    private static function fetchAllRoutes(){

        $directory = APPLICATION_ROUTES_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                self::$routes[] = $directory .$file;
            }
        }
    }

    private static function fetchAllControllers(){

        $directory = APPLICATION_CONTROLLERS_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                self::$controllers[] = $directory .$file;
            }
        }
    }

    private static function fetchAllEntities(){

        $directory = APPLICATION_ENTITIES_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                self::$entities[] = $directory .$file;
            }
        }
    }
    
    private static function fetchAllComponents($dir = APPLICATION_COMPONENTS_FOLDER){

        $directory = $dir;
        $files = scandir($directory);

        foreach($files as $file){
            
            if(is_file($directory . $file))
                self::$components[] = $directory .$file;
            else if($file != '.' && $file != '..' && is_dir($directory . $file))
                self::fetchAllComponents ($directory . $file . '/');
        }
    }

    private static function loadClasses(){

        self::fetchAllClasses();

        foreach(self::$classes as $class)
            require_once $class;
    }
    
    private static function loadComponents(){

        self::fetchAllComponents();

        foreach(self::$components as $component)
            require_once $component;
    }

    private static function loadEntities(){

        self::fetchAllEntities();

        foreach(self::$entities as $entity)
            require_once $entity;
    }

    private static function loadConfigs(){

        self::fetchAllConfigs();

        foreach(self::$configs as $config)
            require_once $config;
    }

    private static function loadControllers(){

        self::fetchAllControllers();

        foreach(self::$controllers as $controller)
            require_once $controller;
    }

    private static function loadRoutes(){

        self::fetchAllRoutes();

        foreach(self::$routes as $route)
            require_once $route;
    }

    private static function loadBundles(){

        self::fetchAllBundles();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                self::loadFilesFromDir($bundle . '/Configs');
                self::loadFilesFromDir($bundle . '/Routes');
                self::loadFilesFromDir($bundle);
                self::loadFilesFromDir($bundle . '/Controllers');
                self::loadFilesFromDir($bundle . '/Entities');
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

    private static function loadFilesFromDir($directory){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = $directory . '/' . $file;

                if(is_file($filepath))
                    require_once $filepath;
            }
        }

        return true;

    }

    private static function checkDependencies(){

        self::$phpVersion = phpversion();
    }
}

AppKernal::initialize();