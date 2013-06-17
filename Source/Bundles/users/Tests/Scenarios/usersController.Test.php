<?php

namespace Application\Bundles\users\Tests;



use Application\Console\BaseTestingRoutine;

class TestusersController extends BaseTestingRoutine
{        
    public function testIndexAction()
    {
        self::$testClass = new \Application\Bundles\users\Controllers\usersController();
            
        $method = 'IndexAction';
        
        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array('case' => 'string'));
    }
}