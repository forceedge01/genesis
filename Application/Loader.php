<?php

namespace Application\Core;



class Loader{

    public static
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
        $path = \Get::Config('APPDIRS.SOURCE_FOLDER') . str_replace('\\','/', $class) . '.php';

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
        $baseFolder = \Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER');
        self::LoadOnceFromDir($baseFolder . $component . '/Config', array('php'));
        $loaderFile = $baseFolder . $component . '/Loader.php';

        if(is_file($loaderFile))
        {
            require_once $baseFolder . $component . '/Loader.php';
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

    public static function LoadEvents($bundle)
    {
        $bundle = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $bundle;
        return self::LoadFilesFromDir($bundle.'/Events');
    }

    public static function LoadEvent($class)
    {
        $event = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . trim(str_replace('\\', '/', $class));
        require_once $event;
    }

    public static function FetchAllBundles(){

        // Include your bundles here

        $bundles = self::AppBundles();

        // Do not edit below this line

        $bundlesDIR = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER');

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
            'EventHandler.Class.php',
            'Request.Class.php',
            'Response.Class.php',
            'Router.Class.php',
            'Cache.Class.php',
            'Template.Class.php',
            'Application.Class.php',
            'Database.Class.php',
            'DatabaseManager.Class.php',
            'Session.Class.php',
            'EventDispatcher.Class.php',
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
            self::$$staticVar = array_merge(self::$$staticVar, self::FetchAllClasses ($dir));
        else
            self::$$staticVar = array_merge(self::$$staticVar, self::FetchAll($dir));

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

            if(is_dir($bundle))
            {
                self::$configs = array_merge(
                        self::$configs,
                        self::LoadFilesFromDir(self::RemoveDoubleSlash($bundle . \Get::Config('APPDIRS.BUNDLES.CONFIG')), array('php'))
                        );
                self::$routes = array_merge(
                        self::$routes,
                        self::LoadFilesFromDir(self::RemoveDoubleSlash($bundle . \Get::Config('APPDIRS.BUNDLES.ROUTES')), array('php'))
                        );
            }
            else
            {
                $params['Backtrace'] = debug_backtrace();
                $message = ' not found in Loader::LoadBundles()';
                require \Get::Config('APPDIRS.TEMPLATING.TEMPLATES_FOLDER') . 'Errors/BundleNotFound.html.php';
                trigger_error ('Unable to locate Bunlde:'. $bundle, E_USER_ERROR);
                die();

            }
        }

    }

    protected static function RemoveDoubleSlash($string)
    {
        return str_replace('//', '/', $string);
    }

    public static function LoadBundle($bundle)
    {
        $bundle = str_replace('//', '/', \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $bundle);

        if(is_dir($bundle))
        {
            self::LoadFilesFromDir($bundle, array('php'), false);
            self::LoadFilesFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.INTERFACES'), array('php'));
            self::LoadFilesFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.CONTROLLERS'), array('php'));
            self::LoadFilesFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.DATABASE_FILES'), array('php'));
        }
        else
        {
            $params['Backtrace'] = debug_backtrace();
            $message = ' not found in Loader::LoadBundle()';
            require \Get::Config('APPDIRS.TEMPLATING.TEMPLATES_FOLDER') . 'Errors/BundleNotFound.html.php';
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

        $loadedFiles = array();

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file($filepath) && self::FileExtensionIs($filepath, $extensions))
                {
                    $loadedFiles[] = self::$LoadedFiles[] = $filepath;
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

        return $loadedFiles;
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
        self::Load('configs', \Get::Config('APPDIRS.CORE.CONFIG_FOLDER'));
        self::Load('interfaces', \Get::Config('APPDIRS.CORE.INTERFACES_FOLDER'));
        self::Load('traits', \Get::Config('APPDIRS.TRAITS_FOLDER'));
        self::Load('classes', \Get::Config('APPDIRS.CORE.LIB_FOLDER'));
//        self::Load('components', \Get::Config('CORE.APPLICATION_COMPONENTS_FOLDER'));
        self::Load('routes', \Get::Config('APPDIRS.STRUCT.ROUTES_FOLDER'));
        self::Load('interfaces', \Get::Config('APPDIRS.STRUCT.INTERFACES_FOLDER'));
        self::Load('models', \Get::Config('APPDIRS.STRUCT.MODELS_FOLDER'));
        self::Load('controllers', \Get::Config('APPDIRS.STRUCT.CONTROLLERS_FOLDER'));
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
        self::LoadFilesFromDir(\Get::Config('APPDIRS.APPLICATION_TESTS_FOLDER'), array('php')) ;

        return self::$LoadedFiles;
    }

    public static function LoadBundleTestFiles()
    {
        $testBundles = array();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                $testBundles[] = $bundle;
                self::LoadFilesFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.TESTS'), array('php'));
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