<?php

namespace Application\Controllers;



use \Application\Core\Application;


class ApplicationController extends Application{

    public function indexAction(){

        $this->forwardTo('users_List');
    }

    public function UnderDevelopmentAction()
    {
        $this->Render(':SiteUnderDevelopment.html.php', 'Site Under Development');
        exit;
    }
}