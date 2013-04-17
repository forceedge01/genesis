<?php

class ApplicationController extends Application{
    
    public function __construct() {
        
        parent::__construct();
        
        $this->prex($this->getObject('Analytics')->getTotalVisits());
    }

    public function indexAction(){

        $this->forwardTo('Welcome');
    }
}