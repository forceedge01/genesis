<?php

namespace Application\Bundles\users\Tests;



use Application\Console\WebTestCase;

class TestusersRepository extends WebTestCase
{
    public function testExampleMethod()
    {
        self::$testClass = new \Application\Bundles\users\Repositories\usersRepository();
        
        $method = '';

        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array('case' => 'array'));
    }
}