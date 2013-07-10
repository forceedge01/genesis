<?php

namespace Bundles\users\Tests;



use Application\Console\WebTestCase;

class TestusersController extends WebTestCase
{        
    public function testIndexAction()
    {
        self::$testClass = new \Bundles\users\Controllers\usersController();
            
        $method = 'IndexAction';
        
        //Checks if the returned value of this function is an integer
//        $this ->AssertTrue($method, array('case' => 'string'));
    }
}