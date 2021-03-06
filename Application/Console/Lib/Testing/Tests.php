<?php

namespace Application\Console\Lib;



class Test extends BaseTestingRoutine{

    private
            $testBundles,
            $testClassesAndComponents,
            $type;

    public static
            $output;

    public function __construct($type = null) {

        parent::__construct();

        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        $this->type = $type;
        $this ->LoadTestFiles();
    }

    public function RunTests($type = null)
    {
        if($type != null)
            $this->type = $type;

        error_reporting(E_ERROR);

        foreach($this -> testClassesAndComponents as $classOrComponent)
        {
            $chunk = explode('.', $classOrComponent);
            $core = 'Application\\Core\\Tests\\'.$chunk[0].'Test';
            $component = 'Application\\Component\\Tests\\'.$chunk[0].'Test';

            if(class_exists($core))
            {
                $this ->CallMethods($core);
            }
            else if(class_exists($component))
            {
                $this ->CallMethods($component);
            }
        }

        foreach ($this -> testBundles as $bundle)
        {
            $object = 'Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Entity';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Repository';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Model';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Controller';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Templates';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }
        }

        $this ->ShowResults();

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    }

    private function CallMethods($object)
    {
        $f = new \ReflectionClass($object);

        $methods = array();

        foreach ($f->getMethods() as $m)
        {
            if(empty($this->type))
            {
                if ($m->class == $object AND strpos(strtolower($m->name), 'test') !== false)
                {
                    $methods[] = $m->name;
                }
            }
            else
            {
                if ($m->class == $object AND strpos(strtolower($m->name), 'test'.$this->type) !== false)
                {
                    $methods[] = $m->name;
                }
            }
        }

        $obj = new $object();

        echo $this ->linebreak(2).'<===================================================================>';

        echo $this ->linebreak(1).'Running test method: ',$this -> blue($object) , '() in file ' . $this -> blue($f ->getFileName()) , $this->linebreak(1);

        echo '<===================================================================>';

        foreach($methods as $method)
        {
            self::$tests += 1;

            echo $this ->linebreak(1),' -> ', $this->blue($method), '();';

            if(ob_get_contents())
                ob_end_clean();

            ob_start();
            $obj -> $method();
            $content = ob_get_clean();

            if(!empty($content))
                echo $content;
            else
                echo $this->linebreak (1),$this->space (4), '- No tests found';
        }
    }

    public function LoadTestFiles()
    {
        require_once ROOT . '/Application/Loader.php';

        \Application\Loader::loadFramework();

        $testClassesAndComponents = \Application\Loader::loadClassesAndComponentsTestFiles();

        foreach($testClassesAndComponents as $classOrComponent)
        {
            $chunks = explode('/', $classOrComponent);
            $this -> testClassesAndComponents[] = end($chunks);
        }

        $this -> testBundles = \Application\Loader::loadBundleTestFiles();

        $b = array();

        foreach($this -> testBundles as $bundle)
        {
            $bd = explode('/', $bundle);
            $b[] = end($bd);
        }

        $this -> testBundles = $b;

        return $this;
    }
}