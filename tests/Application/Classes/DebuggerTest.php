<?php

require '/users/sabhatqureshi/NetBeansProjects/GENESIS/Application/Classes/Debugger.php';

class DebuggerTest extends PHPUnit_Framework_TestCase{

    public function setup(){

        $this->fetch = new Debugger;
    }

    public function testVariables(){

        $this->assertClassHasAttribute('assets', 'debugger');
        $this->assertArrayHasKey('jquery', debugger::$assets);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRunningClass(){

        $this->fetch->run();

    }

    public function testDownloadAssets(){

        $this->fetch->run(array('jquery'));
        $this->assertFileExists('js/jquery.js');
        $this->expectOutputString('your asset has been generated.');
    }
}