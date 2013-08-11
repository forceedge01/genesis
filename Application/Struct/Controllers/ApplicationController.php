<?php

namespace Application\Controllers;



use \Application\Core\Application;


class ApplicationController extends Application{

    public function __construct() {
        parent::__construct();
        $this->BeforeControllerHook();
    }

    public function __destruct() {
        $this->AfterControllerHook();
        parent::__destruct();
    }

    public function indexAction(){

        $this->ForwardTo(\Get::Config('Application.LandingPageRoute'));
    }

    public function UnderDevelopmentAction()
    {
        $this->Render(':UnderDevelopment/SiteUnderDevelopment.html.php', 'Site Under Development');
        exit;
    }
}