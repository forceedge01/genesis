<?php

namespace Application\Core\Interfaces;



interface Debugger {

    /**
     *
     * @param void $var - variable you want to dump to screen
     * Will dump the variable on screen in readable format.
     */
    public static function pre() ;

    /**
     *
     * @param void $var - variable you want to dump to screen
     * Will dump the variable on screen in readable format and then stop execution.
     */
    public static function prex() ;

    /**
     * Display debugging information
     */
    public function debug() ;

    /**
     * @param int $errorNumber
     * @param String $this -> message
     * @param String __FILE__
     * @param int __LINE__
     * @param int $warning
     * @desc trigger an error with line number
     */
    public function ThrowError($warning = E_USER_ERROR) ;

    public function ThrowException() ;

    /**
     *
     * @param type $message
     * @param type $file
     * @param type $line
     * @param type $errorNumber
     * @return \Application\Core\Debugger
     */
    public function SetErrorArgs($message, $file, $line, $errorNumber = 0);

    public static function ThrowStaticError($message, $file = __FILE__, $line = __LINE__);
}