<?php

namespace Application\Bundles\people\Tests;



use Application\Console\BaseTestingRoutine;

class TestpeopleController extends BaseTestingRoutine
{
    public function __construct()
    {
        parent::__construct();
        
        self::$testClass = new \Application\Bundles\people\Controllers\peopleController();
    }

    public function testClassIndexAction()
    {
        $this->AssertMultipleTrue('listAction', array(
            array(
                'case' => 'contains',
                'expected' => 'wrapper',
            ),
            array(
                'case' => 'contains',
                'expected' => 'people',
            ),
            array(
                'case' => 'string',
            )
        ));
    }

    public function testMethodAnotherIndexAction()
    {
        $this->AssertFalse('listAction', array('case' => 'array'));
    }

    public function testRoute()
    {
        $this ->AssertURL('http://localhost/GENESIS/index.php/people/List');
    }
}