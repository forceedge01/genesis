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
            $LoadedFiles = array();

    public static
            $environment,
            $appConfiguration;

    public static function LoadClass($class)
    {
        $path = \Get::Config('CORE.SOURCE_FOLDER') . str_replace('\\','/', $class) . '.php';

        if(is_file($path))
        {
            require_once $path;
        }
        else
        {
            echo "Class '<b>$class</b>' was not found, tried including file with " . __FUNCTION__ ."() from {$path} but file was not found.";
        }
    }

    public static function LoadComponent($component)
    {
        self::LoadOnceFromDir(\Get::Config('CORE.APPLICATION_CONFIGS_FOLDER') . 'Components/' . $component, array('php'));

        $loaderFile = \Get::Config('CORE.APPLICATION_COMPONENTS_FOLDER') . $component . '/Loader.php';

        if(is_file($loaderFile))
        {
            require_once \Get::Config('CORE.APPLICATION_COMPONENTS_FOLDER') . $component . '/Loader.php';
        }
        else
        {
            die("Could not load $component, '<b>$loaderFile</b>' file for this component was not found.");
        }
    }

    public static function AppBundles()
    {
        return array(

            'Welcome',
            'Authentication/users',
        );
    }

    public static function FetchAllBundles(){

        // Include your bundles here

        $bundles = self::AppBundles();

        // Do not edit below this line

        $bundlesDIR = \Get::Config('CORE.BUNDLES_FOLDER');

        foreach($bundles as $bundle){

            self::$bundles[] = $bundlesDIR . str_replace('\\', '/', $bundle);
        }
    }

    private static function FetchAllClasses($classDir){

        $classes = array(

            'Debugger.Class.php',
            'Hooks.Class.php',
            'Variable.Class.php',
            'ObjectManager.Class.php',
            'AppMethods.Class.php',
            'Request.Class.php',
            'Response.Class.php',
            'Router.Class.php',
            'Cache.Class.php',
            'Template.Class.php',
            'Application.Class.php',
            'Database.Class.php',
            'DatabaseManager.Class.php',
            'Session.Class.php',
        );

        foreach($classes as $class){

            if(is_file($classDir . $class)){

                self::$classes[] = $classDir . $class;
            }
            else
                echo '<h1>Class '.$classDir.$class.' not found in kernel::FetchAllClasses</h1>';
        }

        return self::$classes;
    }

    protected static function Load($staticVar, $dir){

        self::$files = array();

        if($staticVar == 'classes')
            self::$$staticVar = self::FetchAllClasses ($dir);
        else
            self::$$staticVar = self::FetchAll($dir);

        foreach(self::$$staticVar as $file)
            require_once $file;
    }

    protected static function LoadDevelopment($staticVar, $dir){

        self::$files = array();

        if($staticVar == 'classes')
            self::$$staticVar = self::FetchAllClasses ($dir);
        else if($staticVar == 'configs')
            self::$$staticVar = self::FetchAllConfigs($dir);
        else
            self::$$staticVar = self::FetchAll($dir);

        foreach(self::$$staticVar as $file)
            require_once $file;
    }

    /**
     * Dev env method
     */
    private static function FetchAllConfigs($dir)
    {
        $directory = $dir;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . str_replace('.php','_dev.php', $file)) && self::FileExtensionIs($directory . $file, array('php')))
            {
                self::$files[] = $directory .str_replace('.php','_dev.php', $file);
            }
            else if($file != '.' && $file != '..' && is_dir($directory . $file))
            {
                self::FetchAllConfigs ($directory . $file . '/');
            }
            else if(is_file($directory.$file) && self::FileExtensionIs($directory . $file, array('php')))
            {
                self::$files[] = $directory . $file;
            }
        }

        return self::$files;
    }

    protected static function LoadBundles(){

        self::FetchAllBundles();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_CONFIGS'), array('php'));
                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_ROUTES'), array('php'));
//                self::LoadFilesFromDir($bundle, array('php'), false);
//                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_INTERFACES'), array('php'));
//                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_CONTROLLERS'), array('php'));
//                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_DATABASE_FILES'), array('php'));
            }
            else{

                $params['Backtrace'] = debug_backtrace();
                $message = ' not found in Loader::LoadBundles()';
                require \Get::Config('CORE.TEMPLATING.TEMPLATES_FOLDER') . 'Errors/BundleNotFound.html.php';
                trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);
                die();

            }
        }

    }

    public static function LoadBundle($bundle)
    {
        $bundle = str_replace('//', '/', \Get::Config('CORE.BUNDLES_FOLDER') . $bundle);

        if(is_dir($bundle))
        {
//            self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_CONFIGS'), array('php'));
            self::LoadFilesFromDir($bundle, array('php'), false);
            self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_INTERFACES'), array('php'));
            self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_CONTROLLERS'), array('php'));
            self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_DATABASE_FILES'), array('php'));
        }
        else
        {
            $params['Backtrace'] = debug_backtrace();
            $message = ' not found in Loader::LoadBundle()';
            require \Get::Config('CORE.TEMPLATING.TEMPLATES_FOLDER') . 'Errors/BundleNotFound.html.php';
            trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);
            die();
        }
    }

    /**
     *
     * @param type $directory
     * @param array $extensions - default php
     * @param type $subdirectories - default true
     * @return boolean
     */
    protected static function LoadFilesFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file($filepath) && self::FileExtensionIs($filepath, $extensions))
                {
                    self::$LoadedFiles[] = $filepath;
                    require $filepath;
                }
                else if($subdirectories)
                {
                    if($file != '.' && $file != '..' && is_dir($filepath))
                    {
                        self::LoadFilesFromDir ($filepath, $extensions);
                    }
                }
            }
        }

        return true;
    }

    /**
     *
     * @param type $directory
     * @param array $extensions - default php
     * @param type $subdirectories - default true
     * @return boolean
     */
    public static function LoadOnceFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file($filepath) && self::FileExtensionIs($filepath, $extensions))
                {
                    self::$LoadedFiles[] = $filepath;
                    require_once $filepath;
                }
                else if($subdirectories)
                {
                    if($file != '.' && $file != '..' && is_dir($filepath))
                    {
                        self::LoadFilesFromDir ($filepath, $extensions);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Dev env method
     */
    protected static function LoadConfigFilesFromDir($directory, array $extensions, $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file(str_replace('.php','_dev.php', $filepath)) && self::FileExtensionIs($filepath, $extensions))
                {
                    self::$LoadedFiles[] = $filepath;
                    require_once $filepath;
                }
                else if($subdirectories)
                {
                    if($file != '.' && $file != '..' && is_dir($filepath))
                    {
                        self::LoadFilesFromDir ($filepath, $extensions);
                    }
                }
                else if(is_file($filepath) && self::FileExtensionIs($filepath, $extensions))
                {
                    self::$LoadedFiles[] = $filepath;
                    require_once $filepath;
                }
            }
        }

        return true;
    }

    protected static function FileExtensionIs($file, array $extensions){

        $exists = false;

        foreach($extensions as $extensions){

            if(pathinfo($file, PATHINFO_EXTENSION) == $extensions){
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    public static function LoadFramework()
    {
//        self::$environment = $environment;

//        if($environment == 'development')
//            self::LoadDevelopment('configs', \Get::Config('CORE.APPLICATION_CONFIGS_FOLDER'));
//        else
        self::Load('configs', \Get::Config('CORE.APPLICATION_CONFIGS_FOLDER').'Core/');
        self::Load('interfaces', \Get::Config('CORE.APPLICATION_CLASSES_FOLDER') . 'Interfaces/');
        self::Load('traits', \Get::Config('CORE.APPLICATION_CLASSES_FOLDER') . 'Traits/');
        self::Load('classes', \Get::Config('CORE.APPLICATION_CLASSES_FOLDER') . 'Core/');
//        self::Load('components', \Get::Config('CORE.APPLICATION_COMPONENTS_FOLDER'));
        self::Load('routes', \Get::Config('CORE.APPLICATION_ROUTES_FOLDER'));
        self::Load('models', \Get::Config('CORE.APPLICATION_MODELS_FOLDER'));
        self::Load('controllers', \Get::Config('CORE.APPLICATION_CONTROLLERS_FOLDER'));
        self::LoadBundles();
    }

    private static function FetchAll($dir){

        $directory = $dir;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file) && self::FileExtensionIs($directory . $file, array('php')))
                self::$files[] = $directory .$file;
            else if($file != '.' && $file != '..' && is_dir($directory . $file))
                self::FetchAll ($directory . $file . '/');
        }

        return self::$files;
    }

    public static function LoadClassesAndComponentsTestFiles()
    {
        self::$LoadedFiles = array();
        self::LoadFilesFromDir(\Get::Config('CORE.APPLICATION_TESTS_FOLDER'), array('php')) ;

        return self::$LoadedFiles;
    }

    public static function LoadBundleTestFiles()
    {
        $testBundles = array();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                $testBundles[] = $bundle;
                self::LoadFilesFromDir($bundle . \Get::Config('CORE.BUNDLES.BUNDLE_TESTS'), array('php'));
            }
        }

        return $testBundles;
    }

    /**
     *
     * @param array $files
     */
    public static function RequireOnce(array $files)
    {
        try
        {
            foreach($files as $file)
                require_once $file;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
}