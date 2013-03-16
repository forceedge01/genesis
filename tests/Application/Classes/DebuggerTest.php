<?php

class ApplicationTest extends PHPUnit_Framework_TestCase{

    public function setup(){

        $this->fetch = new Application;
    }

    public function testIsLoggedIn(){

        $this->assertClassHasAttribute('User', 'Application');
        $this->assertEquals(false, $this->fetch->isLoggedIn());
    }
}