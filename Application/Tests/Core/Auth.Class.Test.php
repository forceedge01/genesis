<?php

namespace Application\Core\Tests;



use Application\Console\BaseTestingRoutine;

class AuthTest extends BaseTestingRoutine {

    public
            $testCandidate;

    public function __construct() {
        parent::__construct();
        $this -> testCandidate = new \Application\Core\Auth();
    }

    public function testBasicOperations()
    {
        $this ->AssertMultipleTrue($this -> testCandidate, 'isValidEmail', array(
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
                    'abc@jlkhasdf'
                ),
                'case' => 'equals',
                'expected' => false
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