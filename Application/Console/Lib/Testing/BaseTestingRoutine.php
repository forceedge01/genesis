<?php

namespace Application\Console\Lib;



use Application\Console\Console;

class BaseTestingRoutine extends Console{

    private static
            $passed,
            $failed,
            $assertions,
            $method,
            $rustart,
            $start_microtime,
            $coverage;

     protected static
            $tests,
            $testClass;

    public function __construct()
    {
        self::$start_microtime = microtime(true);
        self::$rustart = getrusage();

        if(self::$passed == null)
            self::$passed = self::$failed = self::$assertions = self::$tests = self::$coverage = self::$method = 0;
    }

    /**
     *
     * @param type $message
     * @param type $info
     */
    protected static function RegisterPass($message = null, $info = null)
    {
        self::$assertions += 1;
        self::$passed += 1;

        $console = new Console();

        if($message)
            echo $console->linebreak(1), $console->green ($console->space (4).'- '.$message);
        if($info)
            echo $console->linebreak(1), $console->space (6), '- ', $console->blue($info);

        return true;
    }

    /**
     *
     * @param type $message
     * @param type $info
     */
    protected static function RegisterFail($message = null, $info = null)
    {
        self::$assertions += 1;
        self::$failed += 1;

        $console = new Console();

        if($message)
            echo $console->linebreak(1), $console->red ($console->space (4) . '- '.$message);
        if($info)
            echo $console->linebreak(1), $console->space (6), '- ', $console->blue($info);

        return false;
    }

    // Private Methods //

    private function checkMethodExistance($method)
    {
        self::$method = $method;
        return method_exists(self::$testClass, $method);
    }

    // Protected Methods //

    protected function rutime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000)) - ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
    }


    // Public Methods //

    public function ClearResults()
    {
        self::$assertions = 0;
        self::$failed = 0;
        self::$method = '';
        self::$passed = 0;
        self::$rustart = 0;
        self::$start_microtime = 0;
        self::$tests = 0;
    }

    public function AssertGreaterThan($val1, $val2)
    {
        $passed = __FUNCTION__ . '(); '. $val1. ' is greater than '.$val2;
        $failed = __FUNCTION__ . '(); '. $val1. ' is not greater than '.$val2.', failed in class '. get_called_class();

        if($val1 > $val2)
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertLessThan($val1, $val2)
    {
        $passed = $this->green(__FUNCTION__ . '(); '. $val1. ' is less than '.$val2);
        $failed = $this->red(__FUNCTION__ . '(); '. $val1. ' is not less than '.$val2.', failed in class '. get_called_class());

        if($val1 < $val2)
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertNumberOfMethodArguments($method, $numberOfParameters)
    {
        if(!$this -> checkMethodExistance($method))
        {
            self::RegisterFail($this->red(__FUNCTION__ . '(); Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed'));
            return false;
        }
        $passed = $this->green(__FUNCTION__ . '(); '. get_class(self::$testClass). '() -> '.$method.' has '.$numberOfParameters.' of arguments');
        $failed = $this->red(__FUNCTION__ . '(); '. get_class(self::$testClass). '() -> '.$method.' does not have '.$numberOfParameters.' of arguments, failed in class '. get_called_class());

        $classMethod = new \ReflectionMethod(self::$testClass, $method);
        $argumentCount = count($classMethod->getParameters());
        if($argumentCount == $numberOfParameters)
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertArgumentParameterForMethod($method, $argument)
    {
        if(!$this -> checkMethodExistance($method))
        {
            self::RegisterFail($this->red(__FUNCTION__ . '(); Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed'));
            return false;
        }

        $passed = $this->green(__FUNCTION__ . '(); '. get_class(self::$testClass). '() -> '.$method .' has an argument named '.$argument);
        $failed = $this->red(__FUNCTION__ . '(); '. get_class(self::$testClass). '() -> '.$method .' does not have an argument named '.$argument.', failed in class '. get_called_class());

        $classMethod = new \ReflectionMethod(self::$testClass, $method);
        $params = $classMethod->getParameters();

        $pass = 0;
        foreach($params as $obj)
        {
            if($argument == $obj -> name)
            {
                $pass = 1;
            }
        }

        if($pass)
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertArrayHasKey($array, $key)
    {
        $passed = $this->green(__FUNCTION__ . '(); Array has key [ '.$key .' ]');
        $failed = $this->red(__FUNCTION__ . '(); Array does not contain [ '.$key .' ], failed in class '. get_called_class());

        if(array_key_exists($key, $array))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertNotEmpty($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable passed is not empty');
        $failed = $this->red(__FUNCTION__ . '(); Variable passed is '. $variable .', failed in class '. get_called_class());

        if($variable)
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsNumber($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable passed is a number');
        $failed = $this->red(__FUNCTION__ . '(); Variable '. $variable .' is not a number, failed in class '. get_called_class());

        if(is_numeric($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsArray($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable passed is an array');
        $failed = $this->red(__FUNCTION__ . '(); Variable passed is not an array, failed in class '. get_called_class());

        if(is_array($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsObject($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable passed is an object');
        $failed = $this->red(__FUNCTION__ . '(); Variable passed '. $variable .' is not an object, failed in class '. get_called_class());

        if(is_array($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertClassHasParent($class, $parent)
    {
        $passed = $this->green(__FUNCTION__ . '(); '. get_class($class).'() has parent '.$parent);
        $failed = $this->red(__FUNCTION__ . '(); '. get_class($class) .' does not have a parent class '.$parent.', failed in class '. get_called_class());

        if(is_subclass_of($class, $parent))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertClassHasChild($class, $child)
    {
        $passed = $this->green(__FUNCTION__ . '(); '. get_class($class) .'() has child class '.$child);
        $failed = $this->red(__FUNCTION__ . '(); '. get_class($class) .' does not have class, failed in class '. get_called_class());

        if(is_subclass_of($child, $class))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertClassHasProperty($class, $property)
    {
        $passed = $this->green(__FUNCTION__ . '(); '. get_class($class) .'() has property \''.$property.'\'');
        $failed = $this->red(__FUNCTION__ . '(); '. get_class($class) .'() does not have property \''.$property.'\', failed in class '. get_called_class());

        if(property_exists($class, $property))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsFloat($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable '. $variable .' is float');
        $failed = $this->red(__FUNCTION__ . '(); Variable '. $variable .' is not float, failed in class '. get_called_class());

        if(is_float($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsString($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable \''. $variable .'\' is string');
        $failed = $this->red(__FUNCTION__ . '(); Variable \''. $variable .'\' is not a string, failed in class '. get_called_class());

        if(is_string($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsJSON($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable \''. $variable .'\' is JSON object');
        $failed = $this->red(__FUNCTION__ . '(); Variable \''. $variable .'\' is not a JSON object, failed in class '. get_called_class());

        if(json_decode($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
    }

    public function AssertIsBoolean($variable)
    {
        $passed = $this->green(__FUNCTION__ . '(); Variable '. $variable .' is a boolean');
        $failed = $this->red(__FUNCTION__ . '(); Variable '. $variable .' is not a boolean, failed in class '. get_called_class());

        if(is_bool($variable))
        {
            self::RegisterPass($passed);
        }
        else
        {
            self::RegisterFail($failed);
        }
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
    public function AssertTrue($method, array $params)
    {
        if(!$this -> checkMethodExistance($method))
        {
            self::RegisterFail($this->red(__FUNCTION__ . '(); Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed'));
            return false;
        }

        $passed = $this->green(__FUNCTION__ . '(); Test on '. @$params['expected']. ' type '.$params['case'].' passed');
        $failed = $this->red(__FUNCTION__ . '(); Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class().' run on '.get_class(self::$testClass).'() -> '.$method.'();');

        ob_start();

        if(isset($params['parameters']))
        {
            $param = call_user_func_array (array(self::$testClass, $method), $params['parameters']);
        }
        else
        {
            $param = self::$testClass->$method();
        }

        $output = ob_get_clean();

        if(empty($param))
            $param = $output;

        if(isset($params['expected']))
        {
            if(strtolower($params['case']) == 'contains')
            {
                if(is_int(strpos($param, $params['expected'])) and strpos($param, $params['expected'])>=0)
                {
                    self::RegisterPass($passed);
                    return true;
                }
                else
                {
                    self::RegisterFail($failed);
                    return false;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param == $params['expected'])
                {
                    self::RegisterPass($passed);
                    return true;
                }
                else
                {
                    self::RegisterFail($failed);
                    return false;
                }
            }
        }
        else
        {
            $pass = $this->CheckType($param, $params['case']);

            if($pass)
            {
                self::RegisterPass($passed);
                return true;
            }
            else
            {
                self::RegisterFail($failed);
                return false;
            }
        }

        return $this;
    }

    public function AssertFalse($method, array $params)
    {
        if(!$this -> checkMethodExistance($method))
        {
            self::RegisterFail($this->red(__FUNCTION__ . '(); Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed'));
            return false;
        }

        $passed = $this->green(__FUNCTION__ . '(); Test on '. @$params['expected']. ' type '.$params['case'].' passed');
        $failed = $this->red(__FUNCTION__ . '(); Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class());

        ob_start();

        if(isset($params['parameters']))
            $param = call_user_func_array (self::$testClass->$method(), $params['parameters']);
        else
            $param = self::$testClass->$method();

        if(!$param)
            $param = ob_get_clean();

        if($params['expected'])
        {
            if(strtolower($params['case']) == 'contains')
            {
                if(strpos($param, $params['expected']) === false)
                {
                    self::RegisterPass($passed);
                    return true;
                }
                else
                {
                    self::RegisterFail($failed);
                    return false;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param != $params['expected'])
                {
                    self::RegisterPass($passed);
                    return true;
                }
                else
                {
                    self::RegisterFail($failed);
                    return false;
                }
            }
        }
        else
        {
            if($this->CheckType($param, $params['case']))
            {
                self::RegisterFail($failed);
                return false;
            }
            else
            {
                self::RegisterPass($passed);
                return true;
            }
        }

        return $this;
    }

    public function AssertMultipleTrue($method, array $params)
    {
        foreach($params as $array)
            $this ->AssertTrue ($method, $array);
    }

    public function AssertMultipleFalse($method, array $params)
    {
        foreach($params as $array)
            $this ->AssertFalse ($method, $array);
    }



    public function AssertContains($data, $expected)
    {
        if(strpos($data, $expected) >=0 and $data != false)
        {
            self::RegisterPass($this -> green(__FUNCTION__ . '(); Data contains ' . ($expected) . ' passed contains'));
        }
        else
        {
            self::RegisterFail($this -> red(__FUNCTION__ . '(); Data: ' . $expected . ' does not contain '.$expected.' failed contains test in'. get_called_class()));
        }
    }

    public function AssertEquals($data, $expected)
    {
        if($data === $expected)
        {
            self::RegisterPass($this -> green(__FUNCTION__ . '(); Data: ' . $data . ' is equal to '.$expected));
        }
        else
        {
            self::RegisterFail($this -> red(__FUNCTION__ . '(); Got: ' . $data . ', Expected: '.$expected.', failed equals test in '. get_called_class()));
        }
    }

    public function ShowResults() {

        $ru = getrusage();

        echo $this ->linebreak(2);

        if(self::$failed == 0)
            echo $this ->greenOnRed (' Tests: '. $this -> blue(self::$tests) . ', Assertions Passed: ' . $this ->green(self::$passed) . ', Assertions Failed: ' . $this -> red(self::$failed) . ', Total Assertions: '. $this -> blue(self::$assertions) . ', Coverage: ' . self::$coverage . ' ');
        else
            echo $this ->blackOnRed (' Tests: '. $this -> blue(self::$tests) . ', Assertions Passed: ' . $this ->green(self::$passed) . ', Assertions Failed: ' . $this -> red(self::$failed) . ', Total Assertions: '. $this -> blue(self::$assertions) . ', Coverage: ' . self::$coverage . ' ');

        echo $this ->linebreak(2) . $this -> blue("This process used "
            . $this -> rutime($ru, self::$rustart, "utime")
            . " ms for its computations, ") ;

        echo $this -> blue("it spent "
            . $this -> rutime($ru, self::$rustart, "stime")
            . " ms in system calls.") ;

        echo $this->linebreak(1) . $this->blue('Execution took ' . round((microtime(true) - self::$start_microtime)*1000, 4) . ' ms to complete, memory usage: '.  round((memory_get_usage()/1024)/1024, 4) . ' MBytes');

        echo $this ->linebreak(2);
    }

    public function AssertType($data, $type)
    {
        $passed = $this->green(__FUNCTION__ . '(); Test type '.$type.' passed');
        $failed = $this->red(__FUNCTION__ . '(); Test type '.$type.' failed in class '. get_called_class());

        if($this ->CheckType($data, $type))
        {
            self::RegisterPass($passed);
            return true;
        }

        self::RegisterFail($failed);
        return false;
    }

    public function AssertRegularExpression($regex, $content)
    {
        $passed = $this->green(__FUNCTION__ . '(); Regex '.$regex.' found in content passed');
        $failed = $this->red(__FUNCTION__ . '(); Regex '.$regex.' was not found, failed in class '. get_called_class());

        if(preg_match($regex, $content))
        {
            self::RegisterPass($passed);
            return true;
        }

        self::RegisterFail($failed);
        return false;
    }

    private function CheckType($data, $type)
    {
        switch(strtolower($type)){

            case 'integer':

                if(is_int($data))
                    return true;
                return false;

            case 'number':

                if(is_int($data))
                    return true;
                return false;

            case 'string':

                if(!empty($data) AND is_string($data))
                    return true;
                return false;

            case 'json':

                if(json_decode($data))
                    return true;
                return false;

            case 'array':

                if(is_array($data) && !empty($data))
                    return true;
                return false;

            case 'object':

                if(is_object($data))
                    return true;
                return false;

            case 'boolean':

                if($data == false || is_bool($data))
                    return true;
                return false;

            case 'char':

                if(sizeof($data) == 1)
                    return true;
                return false;

            case 'float':

                if(is_float($data))
                    return true;
                return false;

            case 'notNull':

                if(!is_null($data))
                    return true;
                return false;

            default:
                return false;

        }
    }

    public function AssertIsTrue($value)
    {
        if($value === true)
        {
            self::RegisterPass(__FUNCTION__ . '(); Value returned was true');
        }
        else
        {
            self::RegisterFail(__FUNCTION__ . '(); Value got: '.$value.', Expected: true');
        }
    }

    public function AssertIsFalse($value)
    {
        if($value === false)
        {
            self::RegisterPass(__FUNCTION__ . '(); Value returned was false');
        }
        else
        {
            self::RegisterFail(__FUNCTION__ . '(); Value got: '.$value.', Expected: false');
        }
    }
}