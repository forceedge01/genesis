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


    /**
     *
     * @param type $param
     * @param type $params['expected']
     * @param type $params['case']
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
    public function AssertTrue($object, $method, array $params)
    {
        $this -> assertions +=1;

        $with = ' with AssertTrue();';
        $passed = $this->green('Test on '. @$params['expected']. ' type '.$params['case'].' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class().$with).$this->linebreak(1);

        ob_start();

        if(isset($params['parameters']))
        {
            $param = call_user_func_array (array($object, $method), $params['parameters']);
        }
        else
        {
            $param = $object->$method();
        }

        $output = ob_get_clean();

        if(empty($param))
            $param = $output;

        if($params['expected'])
        {
            if(strtolower($params['case']) == 'contains')
            {
                if(strpos($param, $params['expected']) != false)
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param === $params['expected'])
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
        }
        else
        {
            if(strtolower($params['case']) == 'integer')
            {
                if(is_numeric($param))
                {
                    echo $param;

                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'string')
            {
                if(is_string($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'array')
            {
                if(is_array($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'object')
            {
                if(is_object($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'bool')
            {
                if(is_bool($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'char')
            {
                if(sizeof($param) == 1)
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'float')
            {
                if(is_float($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'notNull')
            {
                if(!is_null($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if($param)
            {
                $this -> passed += 1;
                echo $passed;
            }
            else
            {
                $this -> failed += 1;
                echo $failed;
            }
        }

        return $this;
    }

    public function AssertFalse($object, $method, array $params)
    {
        $this -> assertions +=1;

        $with = ' with AssertFalse();';
        $passed = $this->green('Test on '. @$params['expected']. ' type '.$params['case'].' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class().$with).$this->linebreak(1);

        ob_start();

        if(isset($params['parameters']))
            $param = call_user_func_array ($object->$method(), $params['parameters']);
        else
            $param = $object->$method();

        if(!$param)
            $param = ob_get_clean();

        if($params['expected'])
        {
            if(strtolower($params['case']) == 'contains')
            {
                if(strpos($param, $params['expected']) === false)
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param != $params['expected'])
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
        }
        else
        {
            if(strtolower($params['case']) == 'integer')
            {
                if(!is_numeric($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'string')
            {
                if(!is_string($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'array')
            {
                if(!is_array($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'object')
            {
                if(!is_object($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'bool')
            {
                if(!is_bool($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'char')
            {
                if(!sizeof($param) == 1)
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'float')
            {
                if(!is_float($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(strtolower($params['case']) == 'notNull')
            {
                if(is_null($param))
                {
                    $this -> passed += 1;
                    echo $passed;
                }
                else
                {
                    $this -> failed += 1;
                    echo $failed;
                }
            }
            else if(!$param)
            {
                $this -> passed += 1;
                echo $passed;
            }
            else
            {
                $this -> failed += 1;
                echo $failed;
            }
        }

        return $this;
    }

    public function VerifyURL($url)
    {
        $ch = curl_init($url);
        $response = curl_exec($ch);

        return $response;
    }

    public function __destruct() {

        echo $this ->linebreak(2);
        echo 'Passed: ',$this -> passed, '. Failed: ',$this -> failed, '. Assertions: ',$this -> assertions, $this ->linebreak(2);
    }
}