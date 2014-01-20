<?php

namespace Bundles\Authentication\users\Tests;

require_once __DIR__ . '/../Config/users.Test.Config.php';



use Application\Console\Lib\WebTestCase;


class TestusersController extends WebTestCase
{
    public function testIndexAction()
    {
        @self::$testClass = new \Bundles\Authentication\users\Controllers\usersController();

        $method = 'IndexAction';

        //Checks if the returned value of this function is an integer
//        $this ->AssertTrue($method, array('case' => 'string'));
    }
}