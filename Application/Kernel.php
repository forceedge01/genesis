<?php

namespace Application\Core;



require_once __DIR__ . '/Resources/Configs/Core/AppDirs.php';

class AppKernal {

    private static
            $classes = [],
            $configs = [],
            $controllers = [],
            $routes = [],
            $models = [],
            $bundles = [],
            $components = [],
            $files = [],
            $traits = [],
            $interfaces = [];

    public static
            $phpVersion,
            $msyqlVersion;

    private static function fetchAllBundles(){

        // Include your bundles here

        $bundles = array(

            'Welcome',
            'testBundle',
            'neogenesis'
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
            'Getter.php',
            'Request.php',
            'Router.php',
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

    public static function initialize() {

        self::checkDependencies();

        self::load('configs', APPLICATION_CONFIGS_FOLDER);

        self::load('interfaces', APPLICATION_CLASSES_FOLDER . 'Interfaces/');

        self::load('traits', APPLICATION_CLASSES_FOLDER . 'Traits/');

        self::load('classes', APPLICATION_CLASSES_FOLDER . 'Core/');

        self::load('components', APPLICATION_COMPONENTS_FOLDER);

        self::load('routes', APPLICATION_ROUTES_FOLDER);

        self::load('models', APPLICATION_MODELS_FOLDER);

        self::load('controllers', APPLICATION_CONTROLLERS_FOLDER);

        self::loadBundles();

        $route = new Router();

        if(!$route->forwardRequest()){

            echo '<h1>Pattern: ' . $route->GetPattern() . ' Not Found!!</h1>';

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

                require_once APPLICATION_RESOURCES_FOLDER . 'Views/Errors/Bundle_Not_Found.html.php';

                trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);

                exit;

            }

        }

    }

    private static function loadFilesFromDir($directory, array $extensions, $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = $directory . '/' . $file;

                if(is_file($filepath) && self::fileExtensionIs($filepath, $extensions))
                    require_once $filepath;//echo $filepath.'<br />';
                else if($subdirectories){

                    if($file != '.' && $file != '..' && is_dir($filepath))
                        self::loadFilesFromDir ($filepath, $extensions);
                }
            }
        }

        return true;

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