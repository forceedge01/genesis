<?php

namespace Application\Console;



class Components extends Console{

    private $componentName, $componentsFolder;

    public function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        $this->componentsFolder = \Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER');
    }

    public function Create()
    {
        $error = false;

        // Get Component name from user
        $this->componentName = $this->readUser('Enter name of the component you want to build (Unique): ');

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
            echo $this->green ('Component Created Successfully.');
        else
        {
            if($error == 1)
                echo $this->red ('This component already exists.');
            else
                echo $this->red ('Unable to create component, error code: '.$error);
        }

        echo $this->linebreak();
    }

    public function Delete()
    {
        $components = $this->ReadComponents();

        echo $this->linebreak();
        echo $this->blue('List of components in your application: ');
        echo $this->linebreak();
        $this->ShowFormattedArray($components, 1);
        echo $this->linebreak();

        $componentIndex = $this->readUser('Enter component number you want to delete: ');
        $componentName = $components[$componentIndex-1];

        $surity = $this->Choice("Are you sure you want to delete `$componentName` component?", 'Yes');

        echo $this->linebreak ();

        if($surity)
        {
            if($this->DeleteComponent($componentName))
                echo $this->green ("Component {$componentName} Deleted Successfully.");
            else
                echo $this->red('Unable to delete component');

            echo $this->linebreak();
        }
    }

    public function Reset()
    {

    }

    private function DeleteComponent($name)
    {
        if($this->removeDirectory($this->componentsFolder.$name))
            return true;

        return false;
    }

    private function ReadComponents()
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