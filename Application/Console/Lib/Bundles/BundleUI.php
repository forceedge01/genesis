<?php

namespace Application\Console\Lib;



class BundleUI extends BundleAPI {

    public
            $renderMethod;

    public function __construct($type) {

        parent::__construct();
        $this->renderMethod = $type;
    }

    public function createBundle() {

        $this->createConsoleInit();

        if ($this->SetBundle()->CreateBundleFiles())
        {
            echo $this->green("Bundle {$this->bundle} has been created successfully."), $this->linebreak(2);

            if($this->Choice('Do you want to create assets for this bundle?'))
            {
                if($this->CreateAssetFiles())
                {
                    echo $this->green("Assets for bundle {$this->bundle} were successfully created.");
                }
            }

            $greenMessage = 'Please add the following in the Application/Loader.php FetchAllBundles() method: ';
            echo $this->AddBreaks($this->green($greenMessage).$this->blue("'{$this->bundle}'"), 2);
        }
        else
            echo $this->red('Aborting bundle creation for bundle '.$this->bundle.', please check if it already exists!');

        echo $this->linebreak(2);

        return $this;
    }

    private function createConsoleInit()
    {
        if(!isset($_SERVER['SERVER_NAME'])){

            $message = 'Enter namespace of the bundle you want to create (Use "/" instead of "\\". If you are going to use a database table with this bundle, this is usually the singular form of your table name): ';

            echo $this->linebreak(1), $this->blue($message);

            $this->bundle = preg_replace('/bundle/i', '', trim($this->readUser(), '/'));// . 'Bundle';
            $this->name = $this->singular = preg_replace('/bundle/i', '', $this->bundle);

            $this->eventsFolder = $this->Choice('Do you want to create Events for this bundle');
            $this->modelFolder = $this->Choice('Do you want to create a Model for this bundle');

            if(!$this->Choice('Are you sure you want to create this bundle?'))
                exit;
        }

        return $this;
    }

    public function InitCreateAssets()
    {
        echo $this->green('Bundles in your application: '), $this->linebreak(2);
        $bundles = $this->readBundles();
        $index = 1;

        foreach($bundles as $bundle)
        {
            echo "[$index] $bundle".$this->linebreak();
            $index++;
        }

        echo $this->linebreak();
        $choice = $this->readUser('Enter Number: ');
        $bundle = $bundles[$choice-1];

        if($this->Choice('Are you sure you want to create assets for bundle \''.$bundle.'\'?'))
        {
            $this->SetBundle($bundle);

            if($this->CreateAssets())
                echo $this->green ("Assets for bundle '$bundle' have been created successfully.");
            else
                echo $this->red('Unable to create assets of bundle '.$bundle);
        }

        echo $this->linebreak(2);
    }

    public function DeleteAssets()
    {
        echo $this->blue('Bundles in your application'), $this->linebreak(2);
        $this->readBundles(false);
        echo $this->linebreak();
        $choice = $this->readUser('Enter bundle: ');

        if($this->Choice('Are you sure you want to delete assets of bundle \''.$choice.'\'?'))
        {
            if($this->DeleteAsset($choice))
                echo $this->linebreak (1), $this->green ("Assets for bundle '$choice' have been deleted successfully.");
            else
                echo $this->linebreak (1), $this->red('Unable to delete assets of bundle '.$choice);
        }

        echo $this->linebreak(2);
    }

    public function DeleteBundle() {

        if(!isset($_SERVER['SERVER_NAME'])){

            $ans = null;

            echo $this->AddBreaks('Bundles active in your application: '), $this->linebreak();
            $this->readBundles(false);
            $bundleName = $this->readUser($this->linebreak().($this->blue('Enter bundle you want to delete: ')));

            do
            {
                $ans = $this->readUser($this->blue("Are you sure you want to delete $bundleName [yes/no]: "));
                $this->linebreak();
            }
            while($ans == null);
        }
        else
        {
            $bundleName = $this->name;
            $ans = 'yes';
        }

        if($ans == 'yes')
        {
            if(!empty($bundleName))
            {
                echo '... ',$this->linebreak();

                if ($this->DeleteBundleFiles($bundleName))
                {
                    echo $this->green("Bundle {$bundleName} has been deleted successfully."),$this->linebreak(2);

                    $ans = null;

                    do
                    {
                        $ans = $this->decide($this->blue("Do you want to delete assets of {$bundleName} bundle? [yes/no]: "), 'yes');
                    }
                    while($ans == null);

                    if($ans == 'yes')
                    {
                        echo '... '.$this->linebreak();

                        if($this->DeleteAssetFiles($bundleName))
                        {
                            echo $this->green("Assets of bundle {$bundleName} deleted successfully.");
                        }
                        else
                        {
                            echo $this->red("Assets of bundle {$bundleName} were not found");
                        }
                    }
                }
                else
                {
                    echo $this->red("Bundle {$bundleName} was not found");
                }
            }
        }

        echo $this->linebreak(2);

    }

    private function ReplaceBackslashes($string)
    {
        return str_replace('\\','/', $string);
    }

    public function readBundles($return = true) {

        require_once __DIR__ . '/../../../Loader.php';

        $bundles = \Application\Core\Loader::AppBundles();
        $bundlesArray = array();

        foreach ($bundles as $bundle) {

            $bundle = $this->ReplaceBackslashes($bundle);

            if (is_dir($this->bundleSourceFolder .'/'. $bundle))
            {
                if($return)
                    $bundlesArray[] = $bundle;
                else
                    echo $bundle, $this->linebreak();
            }
            else
            {
                echo $this->red("Bundle '$bundle' is registered in class Loader::AppBundles() but was not found in the application structure"), $this->linebreak();
            }
        }

        return $bundlesArray;
    }

    public function Check()
    {
        ob_start();
        $bundles = $this->readBundles();
        $content = ob_get_clean();

        if(!$content)
        {
            echo $this->green ('All bundles registered in Loader were found in the app structure.'), $this->linebreak(2);

            foreach($bundles as $bundle)
            {
                $this->OutputMessages($this->CheckBundleStructure($bundle));
            }
        }
        else
            echo $content;
    }

    protected function CheckBundleStructure($bundle)
    {
        $bundleBase = str_replace('//','/', $this->bundleSourceFolder.$bundle);
        $messages = array();

        $messages['blue'][] = "Verifying structure of '$bundle' Bundle";

        $dir = $bundleBase.$this->bundleConfigsFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleConfigsFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleConfigsFolder}' for bundle '$bundle', required.";


        $dir = $bundleBase.$this->bundleControllersFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleControllersFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleControllersFolder}' for bundle '$bundle', required.";


        $dir = $bundleBase.$this->bundleDatabaseFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleDatabaseFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleDatabaseFolder}' for bundle '$bundle', Optional: needed only if database is used.";


        $dir = $bundleBase.$this->bundleEventsFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleEventsFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleEventsFolder}' for bundle '$bundle', Optional: needed only if using observer pattern.";


        $dir = $bundleBase.$this->bundleViewsFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleViewsFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleViewsFolder}' for bundle '$bundle', required.";


        $dir = $bundleBase.$this->bundleRoutesFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleRoutesFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleRoutesFolder}' for bundle '$bundle', required.";


        $dir = $bundleBase.$this->bundleInterfacesFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleInterfacesFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleInterfacesFolder}' for bundle '$bundle', Optional: Choice of OOP design.";


        $dir = $bundleBase.$this->bundleTestsFolder;
        if(is_dir($dir))
        {
            if($this->IsDirectoryEmpty($dir))
                $messages['red'][] = "Directory has no content: {$this->bundleTestsFolder}";
        }
        else
            $messages['red'][] = "Directory not found: '{$this->bundleTestsFolder}' for bundle '$bundle', Optional: needed only when testing with simplify.";

        if(count($messages) == 1)
            $messages['green'][] = "'$bundle' Bundle structure OK.";

        return $messages;
    }
}