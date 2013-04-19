<?php

class ApplicationController extends Application{

    public function indexAction(){

        $this->forwardTo('Welcome');
    }
}