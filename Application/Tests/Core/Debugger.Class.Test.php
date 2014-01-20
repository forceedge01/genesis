<?php

namespace Application\Core\Tests;



use Application\Console\Lib\WebTestCase;

class DebuggerTest extends WebTestCase {

    public function __construct() {

        parent::__construct();
    }

    public function testPre()
    {
        $this ->AssertMultipleTrue('pre', array(
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

        $this ->AssertRedirect('http://localhost/GENESIS/index.php/people/', 'http://localhost/GENESIS/index.php/login/');

        $array = array('portal' => 123);

        $this->AssertIsArray($array);
        $this->AssertArrayHasKey($array, 'portal');
        $this->AssertEquals($array['portal'], 123);
    }
}