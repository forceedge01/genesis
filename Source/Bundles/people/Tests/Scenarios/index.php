<?php

namespace Application\Bundles\people\Tests;



use Application\Console\BaseTestingRoutine;

class TestpeopleController extends BaseTestingRoutine
{
    public
            $object;

    public function __construct() {

        parent::__construct();
        $this->object = new \Application\Bundles\people\Controllers\peopleController();
    }

    public function testIndexAction()
    {   
        //Checks if the returned value of this function is an integer

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
            ),
        ));

        $this ->AssertTrue($this->object , 'listAction', array(
            'case' => 'string',
        ));
    }

    public function testAnotherIndexAction()
    {

        $this->AssertFalse($this->object, 'listAction', array('case' => 'integer'));
    }
}