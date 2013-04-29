<?php

namespace Application\Core;



class Debugger{

    /**
     *
     * @param void $var - variable you want to dump to screen
     * Will dump the variable on screen in readable format.
     */
    public function pre($var){

        echo '<pre>';

        print_r($var);

        echo '</pre>';
    }

    public function prex($var){

        $this->pre($var);

        exit;
    }

    /**
     * Display debugging information
     */
    public function debug(){

        $this->pre($this);

        echo '<br />Debug data: <br /><br />';

        $this->pre(debug_backtrace());

    }
}