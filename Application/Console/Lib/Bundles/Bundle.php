<?php

namespace Application\Console\Libraries;



use Application\Console\Console;

class Bundle extends Console {

    public
            $name,
            $bundle,
            $singular,
            $renderMethod,
            $bundleFolder;

    private
            $bundleSourceFolder;

    public function __construct($type) {

        $this->renderMethod = $type;
        $this->bundleSourceFolder = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER');
    }

    private function createConsoleInit()
    {
        if(!isset($_SERVER['SERVER_NAME'])){

            $message = 'Enter namespace of the bundle you want to create (Use "/" instead of "\\". If you are using a database with this application, this is usually the singular form of your table name): ';

            echo $this->linebreak(1), $this->blue($message);

            $this->bundle = preg_replace('/bundle/i', '', trim($this->readUser(), '/')) . 'Bundle';
            $this->name = $this->singular = preg_replace('/bundle/i', '', $this->bundle);
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
            $bundleBuilder = new BundleBuilder();
            $bundleBuilder->SetBundle($bundle);

            if($bundleBuilder->CreateAssets())
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
            $bundleBuilder = new BundleBuilder();
            if($bundleBuilder->DeleteAsset($choice))
                echo $this->linebreak (1), $this->green ("Assets for bundle '$choice' have been deleted successfully.");
            else
                echo $this->linebreak (1), $this->red('Unable to delete assets of bundle '.$choice);
        }

        echo $this->linebreak(2);
    }

    public function createBundle() {

        $this->createConsoleInit();

        $bundleBuilder = new BundleBuilder();

        if ($bundleBuilder->SetBundle($this->name)->CreateBundle())//$this->CreateBundleDirs($this->bundleNamespace, $this->bundleSourceFolder))
        {

            echo $this->green("Bundle {$this->name} has been created successfully."), $this->linebreak(2);

            if($this->Choice('Do you want to create assets for this bundle?'))
            {
                if($bundleBuilder->CreateAssets())
                {
                    echo $this->green("Assets for bundle {$this->name} were successfully created.");
                }
            }

            $greenMessage = 'Please add the following in the Application/Loader.php FetchAllBundles() method: ';
            echo $this->AddBreaks($this->green($greenMessage).$this->blue("'{$this->name}'"), 2);
        }
        else
            echo $this->red('Aborting bundle creation for bundle '.$this->name.', please check if it already exists!');

        echo $this->linebreak(2);

        return $this;
    }

    private function CreateBundleDirs($bundle, $prependDir)
    {
        $bundleDirs = explode('/', str_replace(array('\\', '//'), array('//', '/'), $bundle));
        $createDir = $prependDir;

        foreach($bundleDirs as $bundle)
        {
            $createDir .= '/'.$bundle;

            if(!is_dir($createDir))
            {
                if(!mkdir(str_replace('//','/', $createDir)))
                {
                    echo $this->red('Unable to create directory '.$createDir), $this->linebreak();
                    return false;
                }
            }
        }

        return true;
    }

    public function deleteBundle() {

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

                $bundleBuilder = new BundleBuilder();

                if ($bundleBuilder->DeleteBundle($bundleName))
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

                        if($bundleBuilder->DeleteAsset($bundleName))
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
                echo $this->red("Bundle '$bundle' is registered in loader but was not found in the application structure"), $this->linebreak();
            }
        }

        return $bundlesArray;
    }
}