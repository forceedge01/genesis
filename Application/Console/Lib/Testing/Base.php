<?php

namespace Application\Console;



class BaseTestingRoutine extends Console{
    
    protected
            $passed,
            $failed,
            $assertions;
    
    public function __construct() 
    {
        $this -> passed = $this -> failed = $this -> assertions = 0;
    }
    
    public function __destruct() {
        
        echo $this ->linebreak(2);
        echo 'Passed: ',$this -> passed, '. Failed: ',$this -> failed, '. Assertions: ',$this -> assertions, $this ->linebreak(2);
    }
    
    
    /**
     * 
     * @param type $param
     * @param type $compare
     * @param type $case
     * 
     * string
     * integer
     * array
     * object
     * url
     * char
     * chars
     * float
     * 
     * cases: 3 cases: equals, contains, type
     * assert with multiple
     */
    public function AssertTrue($param, $compare = '', $case = 'equals')
    {
        $this -> assertions +=1;
        
        if($compare)
        {
            if($case == 'equals')
            {
                if($param === $compare)
                {
                    $this -> passed += 1;
                    echo 'Test on '. $param. ' passed';
                }
                else
                {
                    $this -> failed += 1;
                    echo 'Test on '. $param. ' failed in class '. get_called_class();
                }
            }
            else
            {
                if(strpos($param, $compare) > -1)
                {
                    $this -> passed += 1;
                    echo 'Test on '. $param. ' passed';
                }
                else
                {
                    $this -> failed += 1;
                    echo 'Test on '. $param. ' failed in class '. get_called_class();
                }    
            }
        }
        else
        {
            if($param)
            {
                $this -> passed += 1;
                echo 'Test on '. $param. ' passed';
            }
            else
            {
                $this -> failed += 1;
                echo 'Test on '. $param. ' failed in class '. get_called_class();
            }
        }
    }
    
    public function AssertFalse($param, $compare = '', $case = 'equals')
    {
        $this -> assertions +=1;
        
        if($compare)
        {
            if($case == '$compare')
            {
                if($param !== $compare)
                {
                    $this -> passed += 1;
                    echo 'Test on '. $param. ' passed';
                }
                else
                {
                    $this -> failed += 1;
                    echo 'Test on '. $param. ' failed in class '. get_called_class();
                }
            }
            else
            {
                if(strpos($param, $compare) == false)
                {
                    $this -> passed += 1;
                    echo 'Test on '. $param. ' passed';
                }
                else
                {
                    $this -> failed += 1;
                    echo 'Test on '. $param. ' failed in class '. get_called_class();
                }    
            }
        }
        else
        {
            if(!$param)
            {
                $this -> passed += 1;
                echo 'Test on '. $param. ' passed';
            }
            else
            {
                $this -> failed += 1;
                echo 'Test on '. $param. ' failed in class '. get_called_class();
            }
        }
    }
    
    public function VerifyURL($url)
    {
        $ch = curl_init($url);
        $response = curl_exec($ch);
        
        return $response;
    }
}