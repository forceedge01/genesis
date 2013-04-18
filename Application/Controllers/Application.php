<?php

class ApplicationController extends Application{
    
    public function __construct() {
        
        parent::__construct();
        
        $this->getObject('Analytics')->recordTrack();
        
        $this->getObject('Analytics')->getTrackReport();
    }

    public function indexAction(){

        $this->forwardTo('Welcome');
    }
}