<?php

namespace Application\Console\Lib;



use Application\Console\Console;

abstract class ComponentsAPI extends Console{

    protected $componentName, $componentsFolder;

    public function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        $this->componentsFolder = \Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER');
    }

    protected function CreateComponent()
    {
        $error = false;

        // Create Component Dirs
        if(!mkdir("{$this->componentsFolder}{$this->componentName}"))
            $error = 1;

        if(!$error)
        {
            if(!mkdir("{$this->componentsFolder}{$this->componentName}/Config"))
                $error = 2;

            // Set content of component files
            $componentLoaderFile = "<?php

    \\Application\\Core\\Loader::LoadOnceFromDir(__DIR__);";

            $componentFile = "<?php

    namespace Application\Components;



    use Application\Core\AppMethods;

    use Application\Core\Debugger;

    class {$this->componentName} extends AppMethods{

    }";

            $configFile = "<?php
    ";

            // Create component files
            if(!$this->createFile("{$this->componentsFolder}{$this->componentName}/Loader.php", $componentLoaderFile))
                $error = 3;

            if(!$this->createFile("{$this->componentsFolder}{$this->componentName}/{$this->componentName}.Component.php", $componentFile))
                $error = 4;

            if(!$this->createFile("{$this->componentsFolder}{$this->componentName}/Config/{$this->componentName}.Config.php", $configFile))
                $error = 5;
        }

        echo $this->linebreak ();

        if(!$error)
            return $this;
        else
        {
            if($error == 1)
                echo $this->red ('This component already exists.');
            else
                echo $this->red ('Unable to create component, error code: '.$error);
        }

        echo $this->linebreak();
    }

    protected function DeleteComponent()
    {
        if($this->RemoveComponent($this->componentName))
            return $this;
        else
            return false;
    }

    private function RemoveComponent($name)
    {
        if($this->removeDirectory($this->componentsFolder.$name))
            return true;

        return false;
    }

    protected function ReadComponents()
    {
        $dir = scandir($this->componentsFolder);

        $clean = array();
        foreach($dir as $directory)
        {
            if(is_dir($this->componentsFolder.$directory) and $directory != '.' and $directory != '..')
                $clean[] = $directory;
        }

        return $clean;
    }
}