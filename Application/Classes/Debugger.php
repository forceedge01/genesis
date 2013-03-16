<?php

class Debugger{

    /**
     *
     * @param void $var - variable you want to dump to screen
     * Will dump the variable on screen in readable format.
     * @assert (1) == 1
     */

    public function pre($var){

        echo '<pre>';

        print_r($var);

        echo '</pre>';

        return $var;
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