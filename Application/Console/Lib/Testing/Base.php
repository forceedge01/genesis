<?php

namespace Application\Console;



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
    
    
    // Private Methods //
    
    private function checkMethodExistance($method)
    {        
        self::$method = $method;
        return method_exists(self::$testClass, $method);
    }
    
    private function updateResult($bool, $passed, $failed)
    {
        if($bool)
        {
            self::$passed += 1;
            echo $passed;
        }
        else
        {
            self::$failed += 1;
            echo $failed;
        }
    }
    
    private function setupCURL($url, $data = null)
    {
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $url);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_HTTP200ALIASES, array(200, 301, 302));

//        curl_setopt($tuCurl, CURLOPT_PORT , 443);
//        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
//        curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
//        curl_setopt($tuCurl, CURLOPT_SSLCERT, getcwd() . "/client.pem");
//        curl_setopt($tuCurl, CURLOPT_SSLKEY, getcwd() . "/keyout.pem");
//        curl_setopt($tuCurl, CURLOPT_CAINFO, getcwd() . "/ca.pem");

        if($data)
        {
            curl_setopt($tuCurl, CURLOPT_POST, 1);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        }
//        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 1);
//        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","SOAPAction: \"/soap/action/query\"", "Content-length: ".strlen($data)));

        $tuData = curl_exec($tuCurl);

        $httpCode = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);

        if($httpCode == 404) {

            curl_close($tuCurl);
            return false;
        }

        else if(!curl_errno($tuCurl))
        {
          $info = curl_getinfo($tuCurl);
          echo $this ->linebreak(1).$this ->green('Took ' . $info['total_time']*1000 . ' ms to send a request to ' . $info['url']) ;
        }
        else
        {
          echo 'Curl error: ' . curl_error($tuCurl);
        }

        curl_close($tuCurl);

        return $tuData;
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
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test '. $val1. ' > '.$val2.' passed'.$with);
        $failed = $this->red('Test '. $val1. ' > '.$val2.' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if($val1 > $val2)
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertLessThan($val1, $val2)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test '. $val1. ' < '.$val2.' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test '. $val1. ' < '.$val2.' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if($val1 < $val2)
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertNumberOfMethodArguments($method, $numberOfParameters)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. get_class(self::$testClass). '() -> '.$method.' passed'.$with);
        $failed = $this->red('Test on '. get_class(self::$testClass). '() -> '.$method.' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        $classMethod = new \ReflectionMethod(self::$testClass, $method);
        $argumentCount = count($classMethod->getParameters());
        if($argumentCount == $numberOfParameters)
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertArgumentParameterForMethod($method, $argument)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. get_class(self::$testClass). '() -> '.$method .' passed'.$with);
        $failed = $this->red('Test on '. get_class(self::$testClass). '() -> '.$method .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
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
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertArrayHasKey($array, $key)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on Array [ '.$key .' ] passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on Array [ '.$key .' ] failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(array_key_exists($key, $array))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertNotEmpty($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if($variable)
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsNumber($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_numeric($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsArray($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_array($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsObject($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable.' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_array($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertClassHasParent($class, $parent)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $class.'() passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $class .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_subclass_of($class, $parent))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertClassHasChild($class, $child)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $class .'() passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $class .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_subclass_of($child, $class))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertClassHasProperty($class, $property)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $class .'() passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $class .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(property_exists($class, $property))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsFloat($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_float($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsString($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_string($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsJSON($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(json_decode($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
        }
    }
    
    public function AssertIsBoolean($variable)
    {
        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test on '. $variable .' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test on '. $variable .' failed in class '. get_called_class() . $with).$this->linebreak(1);
        
        if(is_bool($variable))
        {
            $this ->updateResult(1, $passed, $failed);
        }
        else
        {
            $this ->updateResult(0, $passed, $failed);
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
        self::$assertions +=1;

        $with = ' with '.__FUNCTION__.'();';

        if(!$this -> checkMethodExistance($method))
        {
            echo $this ->linebreak(2).$this->red( 'Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed' . $with );
            self::$failed += 1;
            return false;
        }

        $passed = $this->green('Test on '. @$params['expected']. ' type '.$params['case'].' passed'.$with);
        $failed = $this->linebreak(2).$this->red('Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class().' run on '.get_class(self::$testClass).'() -> '.$method.'();'.$with);

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

        if($params['expected'])
        {
            if(strtolower($params['case']) == 'contains')
            {
                if(is_int(strpos($param, $params['expected'])) and strpos($param, $params['expected'])>=0)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param == $params['expected'])
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
        }
        else
        {
            $pass = $this->CheckType($param, $params['case']);
        }

        $this ->updateResult($pass, $passed, $failed);

        return $this;
    }

    public function AssertFalse($method, array $params)
    {
        self::$assertions +=1;

        $with = ' with '.__FUNCTION__.'();';

        if(!$this -> checkMethodExistance($method))
        {
            echo $this ->linebreak(2).$this->red( 'Method ' . $method . ' for object ' . get_class(self::$testClass) . ' was not found, test failed' . $with );
            self::$failed += 1;
            return false;
        }

        $passed = $this->green('Test on '. @$params['expected']. ' type '.$params['case'].' passed'.$with);
        $failed = $this->red('Test on '. @$params['expected']. ' type '.$params['case'].' failed in class '. get_called_class().$with).$this->linebreak(1);

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
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'equals')
            {
                if($param != $params['expected'])
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
        }
        else
        {
            if($this->CheckType($param, $params['case']))
            {
                $pass = 0;
            }
            else
            {
                $pass = 1;
            }
        }

        $this ->updateResult($pass, $passed, $failed);

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
        self::$assertions +=1;

        if(strpos($data, $expected) >=0 and $data != false)
        {
            echo $this ->linebreak(1) . $this -> green('Data: ' . print_r($expected) . ' passed contains with AssertContains();') ;
            self::$passed += 1;
        }
        else
        {
            echo $this ->linebreak(1) . $this -> red('Data: ' . print_r($expected) . ' failed contains with AssertContains();') ;
            self::$failed += 1;
        }
    }

    public function AssertEquals($data, $expected)
    {
        self::$assertions +=1;

        if($data === $expected)
        {
            echo $this ->linebreak(1) . $this -> green('Data: ' . print_r($expected) . ' passed contains with AssertEquals();') ;
            self::$passed += 1;
        }
        else
        {
            echo $this ->linebreak(1) . $this -> red('Data: ' . print_r($expected) . ' passed contains with AssertEquals();') ;
            self::$failed += 1;
        }
    }

    public function AssertURL($url, $data = null)
    {
        self::$assertions +=1;

        echo $this ->linebreak(2) . $this -> blue('Verifying URL at '.$url) ;

        if($this ->setupCURL($url, $data))
        {
            echo $this ->linebreak(1) . $this -> green('URL: '.$url.' verified with AssertURL();') ;
            self::$passed += 1;
        }
        else
        {
            echo $this ->linebreak(1) . $this -> red('URL: unable to verify URL: '.$url.' with AssertURL();') ;
            self::$failed += 1;
        }
    }

    public function crawlURL($url, $data = null)
    {
        echo $this ->linebreak(2) . $this -> blue('Initiating URL crawl at '.$url) ;

        return $this ->setupCURL($url, urlencode($data)) ;
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
        self::$assertions +=1;

        $with = ' with '.__FUNCTION__.'();';
        $passed = $this->green('Test type '.$type.' passed'.$with);
        $failed = $this->red('Test type '.$type.' failed in class '. get_called_class().$with).$this->linebreak(1);

        $this ->updateResult($this ->CheckType($data, $type), $passed, $failed);
    }

    private function CheckType($data, $type)
    {
        if(strtolower($type) == 'integer')
        {
            if(is_int($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'number')
        {
            if(is_numeric($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'string')
        {
            if(!empty($data) AND is_string($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'json')
        {
            if(json_decode($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'array')
        {
            if(is_array($data) && !empty($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'object')
        {
            if(is_object($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'boolean')
        {

            if($data == false || is_bool($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'char')
        {
            if(sizeof($data) == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'float')
        {
            if(is_float($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if(strtolower($type) == 'notNull')
        {
            if(!is_null($data))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return false;
    }
}