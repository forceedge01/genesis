<?php

namespace Application\Core\Tests;



use Application\Console\BaseTestingRoutine;

class DebuggerTest extends BaseTestingRoutine {
    
    public
            $testCandidate;
    
    public function __construct() {
        parent::__construct();
        $this -> testCandidate = new \Application\Core\Debugger();
    }
    
    public function testPre()
    {
        $this ->AssertMultipleTrue($this -> testCandidate, 'pre', array(
            array
            (
                'parameters' => array(
                    'abc'
                ),
                'case' => 'string',
            ),
            array
            (
                'parameters' => array(
                    array(
                        'abc',
                        '234',
                    )
                ),
                'case' => 'string'
            ),
        ));
        
        $this ->AssertURL('http://localhost/GENESIS/index.php/people');
    }
}