<?php

namespace Application\Core;



class Debugger{

    /**
     *
     * @param void $var - variable you want to dump to screen
     * Will dump the variable on screen in readable format.
     */
    public function pre(){

        $args = func_get_args();
        
        echo '<pre>';

        foreach($args as $var)
            print_r($var);

        echo '</pre>';
    }

    public function prex(){
        
        $args = func_get_args() ;

        foreach($args as $var)
            $this->pre($var);

        exit;
    }

    /**
     * Display debugging information
     */
    public function debug(){

        $this->pre($this);
        
        echo '<br />Libs loaded';
        
        print_r(AppKernal::get());

        echo '<br />Debug data: <br /><br />';

        $this->pre(debug_backtrace());

    }
}