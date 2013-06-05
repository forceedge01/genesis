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
        foreach ($this -> testBundles as $bundle)
        {
            if(is_object('\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Entity'))
            {
                $object = '\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Entity';
                
                $methods = get_class_methods($object);
                
                $obj = new $object();
                
                foreach($methods as $method)
                {
                    $obj -> $method();
                }
            }
            
            if(is_object('\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Repository'))
            {
                $object = '\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Repository';
                
                $methods = get_class_methods($object);
                
                $obj = new $object();
                
                foreach($methods as $method)
                {
                    $obj -> $method();
                }
            }
            
            if(is_object('\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Controller'))
            {
                $object = '\\Application\\Bundles\\'.$bundle.'\\Tests\\Test'.$bundle.'Controller';
                
                $methods = get_class_methods($object);
                
                $obj = new $object();
                
                foreach($methods as $method)
                {
                    $obj -> $method();
                }
            }
        }
    }
    
    public function LoadTestFiles()
    {
        echo 'loading files';
        
        $this -> testBundles = \Application\Core\AppKernal::loadTestFiles();
        
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