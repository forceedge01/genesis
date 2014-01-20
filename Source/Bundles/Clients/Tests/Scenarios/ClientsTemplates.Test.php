<?php

namespace Bundles\Clients\Tests;

require_once __DIR__ . '/../Config/Clients.Test.Config.php';



use Application\Console\TemplateTestCase;


class TestClientsTemplates extends TemplateTestCase
{
    public function testTemplateList()
    {
        $this->AssertTemplate('Clients:list.html.php');
    }

    public function testTemplateCreate()
    {
        $this->AssertTemplate('Clients:create.html.php');
    }

    public function testTemplateEdit()
    {
        $this->AssertTemplate('Clients:edit.html.php');
    }

    public function testTemplateView()
    {
        $this->AssertTemplate('Clients:view.html.php');
    }
}