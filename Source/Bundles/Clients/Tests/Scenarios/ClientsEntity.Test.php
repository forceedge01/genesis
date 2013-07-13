<?php

namespace Bundles\Clients\Tests;

require_once __DIR__ . '/../Config/Clients.Test.Config.php';



use Application\Console\BaseTestingRoutine;


class TestClientsEntity extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::$testClass = new \Bundles\Clients\Entities\ClientsEntity();

        $method = '';

        //Checks if the returned value of this function is an integer
        $this->AssertTrue($method, array('case' => 'string'));
    }
}