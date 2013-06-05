<?php

namespace Application\Console;



class Test extends BaseTestingRoutine{

    private
            $testBundles;

    public function __construct() {
        parent::__construct();

        $this ->LoadTestFiles();
    }

    public function RunTests()
    {
        error_reporting(E_ERROR);

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

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    }

    private function CallMethods($object)
    {
        $f = new \ReflectionClass($object);

        $methods = array();

        foreach ($f->getMethods() as $m)
        {
            if ($m->class == $object AND strpos(strtolower($m->name), 'test') !== false)
            {
                $methods[] = $m->name;
            }
        }

        $obj = new $object();

        echo 'Running test method: ',$object , '()' , $this->linebreak(2);

        foreach($methods as $method)
        {
            echo $this->linebreak(2),'-> ', $method, '();', $this->linebreak(1);
            $obj -> $method();
        }
    }

    public function LoadTestFiles()
    {
        require_once ROOT . '/Application/Loader.php';

        \Application\Core\Loader::loadFramework();

        $this -> testBundles = \Application\Core\Loader::loadTestFiles();

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