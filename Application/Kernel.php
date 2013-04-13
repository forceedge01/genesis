<?php

require_once __DIR__ . '/Configs/AppDirs.php';

class AppKernal {

    private
            $classes = array(),
            $configs = array(),
            $controllers = array(),
            $routes = array(),
            $entities = array(),
            $bundles = array();

    public
            $phpVersion,
            $msyqlVersion;

    private function fetchAllBundles(){

        // Include your bundles here
        
        $bundles = array(

            'Welcome',
            'testBundle',
        );

        // Do not edit below this line

        $bundlesDIR = BUNDLES_FOLDER;

        foreach($bundles as $bundle){

            $this->bundles[] = $bundlesDIR . $bundle;
        }

    }

    private function fetchAllClasses(){

        $classDir = APPLICATION_CLASSES_FOLDER;

        $this->classes[] = $classDir . 'Debugger.php';
        $this->classes[] = $classDir . 'Request.php';
        $this->classes[] = $classDir . 'Router.php';
        $this->classes[] = $classDir . 'HTMLGenerator.php';
        $this->classes[] = $classDir . 'ValidationEngine.php';
        $this->classes[] = $classDir . 'Template.php';
        $this->classes[] = $classDir . 'phpmailer.php';
        $this->classes[] = $classDir . 'Mailer.php';
        $this->classes[] = $classDir . 'Application.php';
        $this->classes[] = $classDir . 'Database.php';
        $this->classes[] = $classDir . 'Auth.php';
        $this->classes[] = $classDir . 'Zip.php';
        $this->classes[] = $classDir . 'Cloner.php';
        $this->classes[] = $classDir . 'Directory.php';
        $this->classes[] = $classDir . 'Session.php';
    }

    public function __construct() {

        $this->checkDependencies();

        session_start();

        $this->loadConfigs();

        $this->loadClasses();

        $this->loadRoutes();

        $this->loadEntities();

        $this->loadControllers();

        $this->loadBundles();
    }

    private function fetchAllConfigs(){

        $directory = APPLICATION_CONFIGS_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                $this->configs[] = $directory .$file;
            }
        }
    }

    private function fetchAllRoutes(){

        $directory = APPLICATION_ROUTES_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                $this->routes[] = $directory .$file;
            }
        }
    }

    private function fetchAllControllers(){

        $directory = APPLICATION_CONTROLLERS_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                $this->controllers[] = $directory .$file;
            }
        }
    }

    private function fetchAllEntities(){

        $directory = APPLICATION_ENTITIES_FOLDER;
        $files = scandir($directory);

        foreach($files as $file){

            if(is_file($directory . $file)){

                $this->entities[] = $directory .$file;
            }
        }
    }

    private function getClasses(){
        return $this->classes;
    }

    private function loadClasses(){

        $this->fetchAllClasses();

        foreach($this->classes as $class)
            require_once $class;
    }

    private function loadEntities(){

        $this->fetchAllEntities();

        foreach($this->entities as $entity)
            require_once $entity;
    }

    private function loadConfigs(){

        $this->fetchAllConfigs();

        foreach($this->configs as $config)
            require_once $config;
    }

    private function loadControllers(){

        $this->fetchAllControllers();

        foreach($this->controllers as $controller)
            require_once $controller;
    }

    private function loadRoutes(){

        unset($_SESSION['Routes']);

        $this->fetchAllRoutes();

        foreach($this->routes as $route)
            require_once $route;
    }

    private function loadBundles(){

        $this->fetchAllBundles();

        foreach($this->bundles as $bundle){

            if(is_dir($bundle)){

                if($this->loadFilesFromDir($bundle . '/Configs') && $this->loadFilesFromDir($bundle . '/Routes') && $this->loadFilesFromDir($bundle . '/Controllers')){

                    if(is_file($bundle . '/Entity.php'))
                        require_once $bundle . '/Entity.php';

                }
                else
                {
                    $message = ' unable to load, check directory structure of bundle, kernel::loadBundles()';

                    $params['Backtrace'] = debug_backtrace();

                    require_once BUNDLES_FOLDER . 'Errors/Templates/ControllerViews/Bundle_Not_Found.html.php';

                    trigger_error('Unable to load Bundle: '.$bundle, E_USER_ERROR);

                    exit;
                }
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

    private function loadFilesFromDir($directory){

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

    private function checkDependencies(){

        $this->phpVersion = phpversion();
    }
}

$AppKernel = new AppKernal();

$route = new Router();

if(!$route->forwardRequest()){

    echo '<h1>Pattern: ' . $route->pattern . ' Not Found!!</h1>';

    exit;

}

