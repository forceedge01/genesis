<?php

namespace Application\Core\Tests;



use Application\Console\BaseTestingRoutine;

class AuthTest extends BaseTestingRoutine {

    public function __construct() {
        parent::__construct();
        self::$testClass = new \Application\Core\Auth();
    }

    public function testBasicOperations()
    {
        $this ->AssertMultipleTrue('isValidEmail', array(
            array
            (
                'parameters' => array(
                    'abc'
                ),
                'case' => 'boolean',
            ),
            array
            (
                'parameters' => array(
                    'abc@abc.com'
                ),
                'case' => 'equals',
                'expected' => true
            ),
        ));
    }
}