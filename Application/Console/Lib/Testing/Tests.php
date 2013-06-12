<?php

namespace Application\Console;



class Test extends BaseTestingRoutine{

    private
            $testBundles,
            $testClassesAndComponents,
            $type;

    public function __construct($type = null) {

        $this->type = $type;

        self::$start_microtime = microtime(true);

        self::$rustart = getrusage();

        parent::__construct();

        $this ->LoadTestFiles();
    }
    
    public function ClearResults()
    {
        self::$assertions = 0;
        self::$failed = 0;
        self::$method = '';
        self::$passed = 0;
        self::$rustart = 0;
        self::$start_microtime = 0;
    }

    public function RunTests()
    {
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
            $object = 'Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Entity';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Repository';

            if(class_exists($object))
            {
                $this->CallMethods($object);
            }

            $object = 'Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Controller';

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

        echo $this ->linebreak(2).'<=========================================================>';

        echo $this ->linebreak(1).'Running test method: ',$this -> blue($object) , '() in file ' . $this -> blue($f ->getFileName()) , $this->linebreak(1);

        echo '<=========================================================>';

        foreach($methods as $method)
        {
            echo $this->linebreak(2),'< - - - - - - - - - - - - - - - - - - - - - - - - - - - - >';
            echo $this ->linebreak(1),' -> ', $method, '();';
            echo $this ->linebreak(1),'< - - - - - - - - - - - - - - - - - - - - - - - - - - - - >';
            $obj -> $method();
        }
    }

    public function LoadTestFiles()
    {
        require_once ROOT . '/Application/Loader.php';

        \Application\Core\Loader::loadFramework();

        $testClassesAndComponents = \Application\Core\Loader::loadClassesAndComponentsTestFiles();

        foreach($testClassesAndComponents as $classOrComponent)
        {
            $chunks = explode('/', $classOrComponent);
            $this -> testClassesAndComponents[] = end($chunks);
        }

        $this -> testBundles = \Application\Core\Loader::loadBundleTestFiles();

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