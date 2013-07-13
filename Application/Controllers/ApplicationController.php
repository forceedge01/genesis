<?php

namespace Application\Controllers;



use \Application\Core\Application;


class ApplicationController extends Application{

    public function indexAction(){

        $this->ForwardTo(\Get::Config('Application.LandingPageRoute'));
    }

    public function UnderDevelopmentAction()
    {
        $this->Render(':SiteUnderDevelopment.html.php', 'Site Under Development');
        exit;
    }
}