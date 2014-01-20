<?php

namespace Bundles\Clients\Tests;

require_once __DIR__ . '/../Config/Clients.Test.Config.php';



use Application\Console\WebTestCase;


class TestClientsController extends WebTestCase
{
    public function testIndexAction()
    {
        self::$testClass = new \Bundles\Clients\Controllers\ClientsController();

        $method = 'IndexAction';

        //Checks if the returned value of this function is an integer
        $this->AssertTrue($method, array('case' => 'string'));
    }
}