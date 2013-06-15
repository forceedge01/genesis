<?php

namespace Application\Bundles\users\Tests;



use Application\Console\BaseTestingRoutine;

class TestusersEntity extends BaseTestingRoutine
{
    public function testExampleMethod()
    {   
        self::$testClass = new \Application\Bundles\users\Entities\usersEntity();
        
        $method = '';

        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array('case' => 'string'));
    }
}