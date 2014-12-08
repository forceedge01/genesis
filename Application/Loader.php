<?php

namespace Application;


use Application\Core\Debugger;

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
            $events = array(),
            $files = array(),
            $LoadedFiles = array(),
            $LoadedBundles = array();

    public static
            $environment,
            $appConfiguration;

    const BOOSTRAP_FILE = 'bootstrap.cache.php';

    public function initLibs()
    {
        require __DIR__ . '/Core/Lib/Set.Class.php';
        require __DIR__ . '/Core/Config/AppDirs.Config.php';
        require __DIR__ . '/Core/Lib/Get.Class.php';

        return $this;
    }

    /**
     *
     * @return type
     * Returns list of bundles included in app
     */
    public function AppBundles()
    {
        return array(
            'Welcome',
            'users',
        );
    }

    public function getFiles()
    {
        return array(
            'Hooks',
            'ObjectManager',
            'AppMethods',
            'DependencyInjector',
            'EventHandler',
            'Server',
            'Request',
            'Response',
            'Application',
            'EventDispatcher',
        );
    }

    /**
     * Loads the framework - consider making protected
     */
    public function LoadFramework()
    {
        $this
            ->Load('interfaces', \Get::Config('APPDIRS.CORE.INTERFACES_FOLDER'))
            ->FetchAllClasses();

        return $this;
    }

    public function loadBundleConfigs()
    {
        Core\Debugger::debugMessage('Loading bundle configuration files');

        $this
            ->LoadCoreStruct()
            ->LoadBundles();
    }

    private function FetchAllClasses()
    {
        $classDir = \Get::Config('APPDIRS.CORE.LIB_FOLDER');
        $classes = $this->getFiles();

        foreach($classes as $class)
        {
            $path = $classDir . $class . '.Class.php';

            if(is_file($path))
            {
                require $path;
            }
            else
            {
                die('<h1>Class '.$path.' not found in Loader->FetchAllClasses</h1>');
            }
        }

        return $this;
    }

    /**
     * Loads framework core library
     */
    private function LoadCoreStruct()
    {
        $this
            ->Load('configs', \Get::Config('APPDIRS.CORE.CONFIG_FOLDER'))
            ->Load('routes', \Get::Config('APPDIRS.STRUCT.ROUTES_FOLDER'))
            ->Load('interfaces', \Get::Config('APPDIRS.STRUCT.INTERFACES_FOLDER'))
            ->Load('events', \Get::Config('APPDIRS.STRUCT.EVENTS_FOLDER'))
            ->Load('models', \Get::Config('APPDIRS.STRUCT.MODELS_FOLDER'))
            ->Load('controllers', \Get::Config('APPDIRS.STRUCT.CONTROLLERS_FOLDER'));

        return $this;
    }

    /**
     *
     * @param string $bundle
     * @return type
     * Loads events for a bundle
     * Consider making protected
     */
    public function LoadEvents($bundle)
    {
        $bundle = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $bundle;

        return $this->LoadOnceFromDir($bundle.'/Events');
    }

    /**
     *
     * @param type $class
     * Legacy
     */
    public function LoadEvent($class)
    {
        $event = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . trim(str_replace('\\', '/', $class));

        require_once $event;
    }

    /**
     * Fetches bundles for inclusion in app.
     * Consider making protected
     */
    public function FetchAllBundles(){

        $bundles = $this->AppBundles();
        $bundlesDIR = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER');

        foreach($bundles as $bundle)
        {
            self::$bundles[] = $bundlesDIR . str_replace('\\', '/', $bundle);
        }
    }

    /**
     *
     * @param type $staticVar
     * @param type $dir
     * Loads files for use for the app
     */
    private function Load($staticVar, $dir){

        self::$files = array();
        self::$$staticVar = array_merge(self::$$staticVar, self::FetchAll ($dir));
        foreach(self::$$staticVar as $file)
            require_once $file;

        return $this;
    }

    /**
     * Loads config and routes of all registered bundles for usage throughout the app.
     */
    private function LoadBundles(){

        $this->FetchAllBundles();
        $bundleConfigDir = \Get::Config('APPDIRS.BUNDLES.CONFIG');
        $bundleRoutesDir = \Get::Config('APPDIRS.BUNDLES.ROUTES');

        foreach(self::$bundles as $bundle)
        {
            if(is_dir($bundle))
            {
                $configs = $this->LoadOnceFromDir($bundle . $bundleConfigDir, array('php'));

                if(is_array($configs))
                {
                    self::$configs = array_merge(
                        self::$configs,
                        $configs
                        );
                }

                $routes = $this->LoadOnceFromDir($bundle . $bundleRoutesDir, array('php'));

                if(is_array($routes))
                {
                    self::$routes = array_merge(
                        self::$routes,
                        $routes
                        );
                }
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

    /**
     *
     * @param type $bundle
     */
    public function LoadBundle($bundle)
    {
        if(!in_array($bundle, self::$LoadedBundles))
        {
            self::$LoadedBundles[] = $bundle;
            $bundle = $this->GetBundleAbsolutePath($bundle);

            if(is_dir($bundle))
            {
                $this
                    ->LoadOnceFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.INTERFACES'))
                    ->LoadOnceFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.CONTROLLERS'));
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

    private function GetBundleAbsolutePath($bundle)
    {
        return str_replace('//', '/', \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $bundle);
    }

    /**
     *
     * @param type $bundle
     * @return type
     */
    public function LoadBundleEntities($bundle)
    {
        return $this->LoadOnceFromDir($this->GetBundleAbsolutePath($bundle) . \Get::Config('APPDIRS.BUNDLES.DATABASE_FILES').'Entities', array('php'));
    }

    /**
     *
     * @param type $bundle
     * @return type
     */
    public function LoadBundleRepositories($bundle)
    {
        return $this->LoadOnceFromDir($this->GetBundleAbsolutePath($bundle) . \Get::Config('APPDIRS.BUNDLES.DATABASE_FILES').'Repositories', array('php'));
    }

    /**
     *
     * @param type $bundle
     * @return type
     */
    public function LoadBundleModel($bundle)
    {
        return $this->LoadOnceFromDir($this->GetBundleAbsolutePath($bundle) . \Get::Config('APPDIRS.BUNDLES.DATABASE_FILES'), array('php'));
    }

    /**
     * @param type $directory
     * @param array $extensions - default php
     * @param type $subdirectories - default true
     * @return boolean Requires all files in the current directory along with files in subdirectories<br /> presedence of files in subdirectory
     */
    public function LoadOnceFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        $loadedFiles = array();

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = $directory . '/' . $file;

                if($subdirectories and $file != '.' && $file != '..' && is_dir($filepath))
                {
                    $this->LoadOnceFromDir ($filepath, $extensions, $subdirectories);
                }
                else if(is_file($filepath) && self::FileExtensionIs($filepath, $extensions))
                {
                    $loadedFiles = self::$LoadedFiles[] = $filepath;
                    require_once $filepath;
                }
            }
        }

        return $this;
    }

    /**
     * Dev env method
     */
    private function LoadConfigFilesFromDir($directory, array $extensions = array('php'), $subdirectories = true){

        if(is_dir($directory)){

            $files = scandir($directory);

            foreach($files as $file){

                $filepath = str_replace('//', '/', $directory . '/' . $file);

                if(is_file(str_replace('.php','_dev.php', $filepath)) && $this->FileExtensionIs($filepath, $extensions))
                {
                    self::$LoadedFiles[] = $filepath;
                    require_once $filepath;
                }
                else if($subdirectories)
                {
                    if($file != '.' && $file != '..' && is_dir($filepath))
                    {
                        $this->LoadOnceFromDir ($filepath, $extensions);
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

    private function FileExtensionIs($file, array $extensions){

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
     *
     * @param type $class
     * @return boolean
     * Loads a component
     */
    public function LoadComponent($class)
    {
        $base = \Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER');
        $chunks = explode('\\', $class);

        $loader = $base . $chunks[0] . '/Loader.php';

        if(! is_file($loader))
        {
            Debugger::ThrowStaticError("Could not load $class, '<b>$loader</b>' file for this component was not found.", __FILE__, __LINE__);
        }

        require $loader;

        return $this;
    }

    private function FetchAll($dir){

        $directory = $dir;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file) && $this->FileExtensionIs($directory . $file, array('php')))
                self::$files[] = $directory .$file;
            else if($file != '.' && $file != '..' && is_dir($directory . $file))
                $this->FetchAll ($directory . $file . '/');
        }

        return self::$files;
    }

    public function LoadClassesAndComponentsTestFiles(array $components = array())
    {
        self::$LoadedFiles = array();
        $this->LoadOnceFromDir(\Get::Config('APPDIRS.APPLICATION_TESTS_FOLDER'), array('php'));

        foreach($components as $component)
        {
            $this->LoadOnceFromDir(\Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER').$component.'/Tests/', array('php'));
        }

        return self::$LoadedFiles;
    }

    public function LoadBundleTestFiles()
    {
        $testBundles = array();

        foreach(self::$bundles as $bundle){

            if(is_dir($bundle)){

                $testBundles[] = $bundle;
                $this->LoadOnceFromDir($bundle . \Get::Config('APPDIRS.BUNDLES.TESTS'), array('php'));
            }
        }

        return $testBundles;
    }

    /**
     *
     * @param array $files
     */
    public function RequireOnce(array $files)
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

    private function getBoostrapPath()
    {
        return __DIR__ . '/' . self::BOOSTRAP_FILE;
    }

    public function LoadBoostrap()
    {
        if(! file_exists(self::getBoostrapPath()))
        {
            $this->createBoostrap(__DIR__ . '/Core');
        }

        require_once self::getBoostrapPath();
    }

    private function createBoostrap($folder)
    {
        $files = $this->getFiles();

        ob_start();
        foreach($files as $file)
        {
            echo php_strip_whitespace($folder . '/Interfaces/' . $file . '.Interface.php');
            echo php_strip_whitespace($folder . '/Lib/' . $file . '.Class.php');
        }

        $contents = $this->sanitizeContentsForOnePHPFile(ob_get_clean());

        file_put_contents($this->getBoostrapPath(), $contents);
    }

    private function sanitizeContentsForOnePHPFile($contents)
    {
        $removedMultiplePHPSymbols = str_replace(['<?php', '<?', '?>'], '', $contents);

        return '<?php // GENESIS Bootstrap file' . $removedMultiplePHPSymbols;
    }

    public function LoadGenesis() {

        Core\Debugger::debugMessage('Loading Genesis');

        AppKernal::$scriptStartTime = microtime(true);
        $this->initLibs()
//            ->CheckDependencies()
            ->LoadFramework()
            ;

        Core\Debugger::debugMessage('Loaded Genesis');

        return $this;
    }

    public function getAppInstance()
    {
        Core\Debugger::debugMessage('Instantiating new Application');

        return new Core\Application();
    }

    /**
     *
     * @param type $fileType
     * @return type
     * Gets info on file types loaded
     */
    public function Get($fileType = null){

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

        return self::$fileType;
    }
}
