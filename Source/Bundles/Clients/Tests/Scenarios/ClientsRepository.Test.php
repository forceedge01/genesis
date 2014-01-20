<?php

namespace Bundles\Clients\Tests;

require_once __DIR__ . '/../Config/Clients.Test.Config.php';


use Application\Console\BaseTestingRoutine;


class TestClientsRepository extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::$testClass = new \Bundles\Clients\Repositories\ClientsRepository();

        $method = '';

        //Checks if the returned value of this function is an integer
        $this->AssertTrue($method, array('case' => 'array'));
    }
}