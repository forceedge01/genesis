<?php

namespace Application\Core\Struct\Controllers;



use Application\Core\Lib\Controller;


class ApplicationController extends Controller{
    public function indexAction(){
        $this->ForwardTo(\Get::Config('Application.LandingPageRoute'));
    }
}
