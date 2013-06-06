<?php

namespace Application\Bundles\people\Tests;



use Application\Console\BaseTestingRoutine;

class TestpeopleEntity extends BaseTestingRoutine
{
    public function testIndexAction()
    {
        $test = new \Application\Bundles\people\Controllers\peopleController();
        
        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($test ->indexAction(), 'equals', array(
            
        ));
    }
}