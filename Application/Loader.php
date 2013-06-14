<?php

namespace Application\Core;



class Loader{

    protected static
            $classes = array() ,
            $configs = array() ,
            $controllers = array() ,
            $routes = array() ,
            $models = array() ,
            $bundles = array() ,
            $components = array() ,
            $traits = array() ,
            $interfaces = array(),
            $files = array(),
            $loadedFiles = array();
    
    public static 
            $appConfiguration = array();

    private static function fetchAllBundles(){

        // Include your bundles here

        $bundles = array(

            'Welcome',
            'neogenesis',
            'people'
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
            'Variable.php',
            'AppMethods.php',
            'Get.php',
            'Manager.php',
            'Request.php',
            'Response.php',
            'Router.php',
            'Cache.php',
            'Template.php',
            'Application.php',
            'Database.php',
            'Auth.php',
            'Session.php',

        );

        foreach($classes as $class){

            if(is_file($classDir . $class)){

                self::$classes[] = $classDir . $class;
            }
            else
                echo '<h1>Class '.$classDir.$class.' not found in kernel::fetchAllClasses</h1>';
        }

        return self::$classes;
    }

    protected static function load($staticVar, $dir){

        self::$files = array();

        if($staticVar == 'classes')
            self::$$staticVar = self::fetchAllClasses ($dir);
        else
            self::$$staticVar = self::fetchAll($dir);

        foreach(self::$$staticVar as $file)
            require_once $file;
    }

    protected static function loadBundles(){

        self::fetchAllBundles();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                self::loadFilesFromDir($bundle . BUNDLE_CONFIGS, array('php'));
                self::loadFilesFromDir($bundle . BUNDLE_ROUTES, array('php'));
                self::loadFilesFromDir($bundle, array('php'), false);
                self::loadFilesFromDir($bundle . BUNDLE_INTERFACES, array('php'));
                self::loadFilesFromDir($bundle . BUNDLE_CONTROLLERS, array('php'));
                self::loadFilesFromDir($bundle . BUNDLE_DATABASE_FILES, array('php'));
                
            }
            else{

                $params['Backtrace'] = debug_backtrace();

                $message = ' not found in kernel::loadBundles()';

                require APPLICATION_RESOURCES_FOLDER . 'Views/Errors/Bundle_Not_Found.html.php';

                trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);

                exit;

            }
        }

    }

    protected static function loadFilesFromDir($directory, array $extensions, $subdirectories = true){
        
        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file($filepath) && self::fileExtensionIs($filepath, $extensions))
                {
                    self::$loadedFiles[] = $filepath;
                    require $filepath;
                    
                    if(isset($config))
                        self::$appConfiguration = $config;
                }
                else if($subdirectories)
                {
                    if($file != '.' && $file != '..' && is_dir($filepath))
                    {
                        self::loadFilesFromDir ($filepath, $extensions);
                    }
                }
            }
        }

        return true;

    }

    protected static function fileExtensionIs($file, array $extensions){

        $exists = false;

        foreach($extensions as $extensions){

            if(pathinfo($file, PATHINFO_EXTENSION) == $extensions){
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    public static function loadFramework()
    {
        require_once APPLICATION_CLASSES_FOLDER . 'Core/Set.php';
        
        self::load('configs', APPLICATION_CONFIGS_FOLDER);

        self::load('interfaces', APPLICATION_CLASSES_FOLDER . 'Interfaces/');

        self::load('traits', APPLICATION_CLASSES_FOLDER . 'Traits/');

        self::load('classes', APPLICATION_CLASSES_FOLDER . 'Core/');

        self::load('components', APPLICATION_COMPONENTS_FOLDER);

        self::load('routes', APPLICATION_ROUTES_FOLDER);

        self::load('models', APPLICATION_MODELS_FOLDER);

        self::load('controllers', APPLICATION_CONTROLLERS_FOLDER);

        self::loadBundles();
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
    
    public static function loadClassesAndComponentsTestFiles()
    {
        self::$loadedFiles = array();
        self::loadFilesFromDir(APPLICATION_TESTS_FOLDER, array('php')) ;
        
        return self::$loadedFiles;
    }

    public static function loadBundleTestFiles()
    {
        $testBundles = array();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                $testBundles[] = $bundle;
                self::loadFilesFromDir($bundle . BUNDLE_TESTS, array('php'));
            }
        }

        return $testBundles;
    }
}