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

        $this->AssertTrue($this->object, 'viewAction', array(
            'parameters' => array(
               2
            ),
            'case' => 'string',
        ));

        $this ->AssertTrue($this->object , 'listAction', array(
            'case' => 'integer',
        ));
    }

    public function testAnotherIndexAction()
    {

        $this->AssertFalse($this->object, 'listAction', array('case' => 'string'));
    }
}