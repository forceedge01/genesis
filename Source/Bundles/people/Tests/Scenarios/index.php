<?php

namespace Application\Bundles\people\Tests;



use Application\Console\BaseTestingRoutine;

class TestpeopleController extends BaseTestingRoutine
{
    public
            $object;

    public function __construct()
    {
        parent::__construct();
        $this->object = new \Application\Bundles\people\Controllers\peopleController();
    }

    public function testClassIndexAction()
    {
        $this->AssertMultipleTrue($this->object, 'listAction', array(
            array(
                'case' => 'contains',
                'expected' => 'wrapper',
            ),
            array(
                'case' => 'contains',
                'expected' => 'HTML',
            ),
            array(
                'case' => 'contains',
                'expected' => 'people',
            )
        ));
    }

    public function testMethodAnotherIndexAction()
    {
        $this->AssertFalse($this->object, 'listAction', array('case' => 'integer'));
    }

    public function testRoute()
    {
        $this ->AssertURL('http://localhost/GENESIS/index.php/people/List');
    }
}