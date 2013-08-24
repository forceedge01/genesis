<?php

namespace Application\Core;



class Loader extends Debugger{

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
            $LoadedFiles = array(),
            $LoadedBundles = array();

    public static
            $environment,
            $appConfiguration;

    /**
     *
     * @return type
     * Returns list of bundles included in app
     */
    public static function AppBundles()
    {
        return array(

            'Welcome',
            'Authentication/users',
        );
    }

    /**
     *
     * @param type $class
     * Registered autoloader
     */
    public static function LoadClass($class)
    {
        $dir = str_replace('\\','/', $class);
        $className = explode('/', $dir);

        $path = ROOT . $dir . '/'.end($className);

        if(is_file($path.'.Class.php'))
        {
            require_once $path.'.Class.php';
        }
        else if(is_file($path.'.Component.php'))
        {
            require_once $path.'.Component.php';
        }
        else if(is_file($path.'.php'))
        {
            require_once $path.'.php';
        }
        else
        {
            Debugger::ThrowStaticError("Class '<b>$class</b>' was not found, tried including file with " . __FUNCTION__ ."() from <b>{$path}.|Class|Component|.php</b> but file was not found, called from ".  get_called_class());
        }
    }

    /**
     * Loads the framework - consider making protected
     */
    public static function LoadFramework()
    {
        self::LoadCore();
        self::GetComponents();
        self::LoadBundles();
    }

    /**
     * Loads framework core library
     */
    public static function LoadCore()
    {
        self::Load('configs', \Get::Config('APPDIRS.CORE.CONFIG_FOLDER'));
        self::Load('interfaces', \Get::Config('APPDIRS.CORE.INTERFACES_FOLDER'));
        self::Load('traits', \Get::Config('APPDIRS.TRAITS_FOLDER'));
        self::RequireAllClasses(\Get::Config('APPDIRS.CORE.LIB_FOLDER'));
        self::Load('routes', \Get::Config('APPDIRS.STRUCT.ROUTES_FOLDER'));
        self::Load('interfaces', \Get::Config('APPDIRS.STRUCT.INTERFACES_FOLDER'));
        self::Load('models', \Get::Config('APPDIRS.STRUCT.MODELS_FOLDER'));
        self::Load('controllers', \Get::Config('APPDIRS.STRUCT.CONTROLLERS_FOLDER'));
    }

    /**
     *
     * @param type $component
     * @return boolean
     * Loads a component
     */
    public static function LoadComponent($component)
    {
        if(in_array($component, self::$components))
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
                Debugger::ThrowStaticError("Could not load $component, '<b>$loaderFile</b>' file for this component was not found.", __FILE__, __LINE__);
            }

            return true;
        }
        else
            self::ThrowStaticError('Component: '.$component. ' not found! Components found: <pre>'.print_r(Loader::$components, true).'</pre>', __FILE__, __LINE__);

        return false;
    }

    /**
     *
     * @param string $bundle
     * @return type
     * Loads events for a bundle
     * Consider making protected
     */
    public static function LoadEvents($bundle)
    {
        $bundle = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $bundle;
        return self::LoadFilesFromDir($bundle.'/Events');
    }

    /**
     *
     * @param type $class
     * Legacy
     */
    public static function LoadEvent($class)
    {
        $event = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . trim(str_replace('\\', '/', $class));
        require_once $event;
    }

    /**
     * Fetches bundles for inclusion in app.
     * Consider making protected
     */
    public static function FetchAllBundles(){

        $bundles = self::AppBundles();
        $bundlesDIR = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER');

        foreach($bundles as $bundle)
        {
            self::$bundles[] = $bundlesDIR . str_replace('\\', '/', $bundle);
        }
    }

    /**
     *
     * @param type $classDir
     * @return type
     */
    private static function RequireAllClasses($classDir){

        $classes = array(

            'Debugger.Class.php',
            'Hooks.Class.php',
            'Variable.Class.php',
            'ObjectManager.Class.php',
            'AppMethods.Class.php',
            'DependencyInjector.Class.php',
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

        foreach($classes as $class)
        {
            if(is_file($classDir . $class)){
                require_once $classDir . $class;
            }
            else
                die('<h1>Class '.$classDir.$class.' not found in kernel::FetchAllClasses</h1>');
        }
    }

    /**
     *
     * @param type $staticVar
     * @param type $dir
     * Loads files for use for the app
     */
    protected static function Load($staticVar, $dir){

        self::$files = array();
        self::$$staticVar = array_merge(self::$$staticVar, self::FetchAll ($dir));
        foreach(self::$$staticVar as $file)
            require_once $file;
    }

    /**
     * Loads config and routes of all registered bundles for usage throughout the app.
     */
    protected static function LoadBundles(){

        self::FetchAllBundles();
        $bundleConfigDir = \Get::Config('APPDIRS.BUNDLES.CONFIG');
        $bundleRoutesDir = \Get::Config('APPDIRS.BUNDLES.ROUTES');

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle))
            {
                self::$configs = array_merge(
                        self::$configs,
                        self::LoadFilesFromDir($bundle . $bundleConfigDir, array('php'))
                        );
                self::$routes = array_merge(
                        self::$routes,
                        self::LoadFilesFromDir($bundle . $bundleRoutesDir, array('php'))
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
        if(!in_array($bundle, self::$LoadedBundles))
        {
            self::$LoadedBundles[] = $bundle;
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
    }

    /**
     *
     * @param type $directory
     * @param array $extensions - default php
     * @param type $subdirectories - default true
     * @return array Returns files included, uses require
     */
    protected static function LoadFilesFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        $loadedFiles = array();

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = $directory . '/' . $file;

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
     * @param type $directory
     * @param array $extensions - default php
     * @param type $subdirectories - default true
     * @return boolean Requires all files in the current directory along with files in subdirectories<br /> presedence of files in subdirectory
     */
    public static function LoadOnceFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if($subdirectories and $file != '.' && $file != '..' && is_dir($filepath))
                {
                    self::LoadOnceFromDir ($filepath, $extensions, $subdirectories);
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

    /**
     * Dev env method
     */
    protected static function LoadConfigFilesFromDir($directory, array $extensions = array('php'), $subdirectories = true){

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

        foreach($extensions as $extension)
        {
            if(pathinfo($file, PATHINFO_EXTENSION) == $extension)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets a list of components available in the application.
     */
    private static function GetComponents()
    {
        $base = \Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER');
        $components = scandir($base);
        foreach($components as $component)
        {
            if($component != '.' and $component != '..')
            {
                if(is_file($base.'/'.$component.'/Loader.php'))
                {
                    self::$components[] = $component; continue;
                }

                self::$components[] = $component . ' (Broken: Loader.php for component not found.)';
            }
        }
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