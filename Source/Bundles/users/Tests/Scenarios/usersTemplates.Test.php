<?php

namespace Bundles\users\Tests;



use Application\Console\WebTestCase;

class TestusersTemplates extends WebTestCase
{
    public function testTemplateExampleMethod()
    {
        self::$testClass = new \Bundles\users\Repositories\usersRepository();

        $method = '';

        //Checks if the returned value of this function is an integer
//        $this ->AssertTrue($method, array('case' => 'array'));
    }

    public function testTemplateList()
    {
        $this->AssertTemplateMultiple('users:login.html.php', array(

            'input[type=text]|[name=username]',
            'input[type=password]|[name=password]',
            'input[type=submit]|[name=login]|[value=login]',
            'input[type=text]'
        ));
    }

    public function testTemplateCreate()
    {
        $this->AssertTemplate('users:create.html.php');
    }

    public function testTemplateEdit()
    {
        $this->AssertTemplate('users:edit.html.php');
    }

    public function testTemplateView()
    {
        $this->AssertTemplate('users:view.html.php');
    }
}