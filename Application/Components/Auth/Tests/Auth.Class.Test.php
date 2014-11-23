<?php

namespace Application\Core\Tests;



use Application\Console\Lib\WebTestCase;

class AuthTest extends WebTestCase {

    public function __construct() {
        parent::__construct();
        self::$testClass = new \Application\Components\Auth;
    }

    public function testMethodIsValidEmail()
    {
        $this ->AssertMultipleTrue('isValidEmail', array(
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
                    'abc@abc.com'
                ),
                'case' => 'equals',
                'expected' => true
            ),
        ));
    }

    public function testMethodForwardToLoginPage()
    {
        $this->AssertFlashMessage('ForwardToLoginPage', 'this is a custom message', 'this is a custom message');
    }

    public function testMethodLogout()
    {

    }

    public function testMethodAuthenticateUser()
    {

    }

    public function testMethodGeneratePassword()
    {

    }

    public function testMethodGeneratePasswordHash()
    {

    }

    public function testMethodForwardToLoggedInPage()
    {

    }

    public function testMethodIsLoggedIn()
    {

    }

    public function testMethodGetUser()
    {

    }
}