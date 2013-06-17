<?php

namespace Application\Core\Controllers;



use \Application\Core\Application;


class ApplicationController extends Application{

    public function indexAction(){

        $this->forwardTo('users_List');
    }
}