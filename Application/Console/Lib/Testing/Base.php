<?php

namespace Application\Console;



class BaseTestingRoutine extends Console{

    protected static
            $passed,
            $failed,
            $assertions;

    public function __construct()
    {
        if(self::$passed == '')
            self::$passed = self::$failed = self::$assertions = 0;
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
        self::$assertions +=1;

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
            if(strtolower($params['case']) == 'integer')
            {
                if(is_int($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'number')
            {
                if(is_numeric($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'string')
            {
                if(is_string($param) AND !empty($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'json')
            {
                if(json_decode($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'array')
            {
                if(is_array($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'object')
            {
                if(is_object($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'boolean')
            {
                echo $param;
                if(is_bool($param) || $param == false)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'char')
            {
                if(sizeof($param) == 1)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'float')
            {
                if(is_float($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'notNull')
            {
                if(!is_null($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if($param)
            {
                $pass = 1;
            }
            else
            {
                $pass = 0;
            }
        }
        
        $this ->updateResult($pass, $passed, $failed);
        
        unset($object);

        return $this;
    }

    public function AssertFalse($object, $method, array $params)
    {
        self::$assertions +=1;

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
            if(strtolower($params['case']) == 'integer')
            {
                if(!is_int($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'number')
            {
                if(!is_numeric($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'string')
            {
                if(!is_string($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'json')
            {
                if(!json_decode($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'array')
            {
                if(!is_array($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'object')
            {
                if(!is_object($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'boolean')
            {
                if(!is_bool($param) || !is_null($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'char')
            {
                if(!sizeof($param) == 1)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'float')
            {
                if(!is_float($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($params['case']) == 'notNull')
            {
                if(is_null($param))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(!$param)
            {
                $pass = 1;
            }
            else
            {
                $pass = 0;
            }
        }
        
        $this ->updateResult($pass, $passed, $failed);

        return $this;
    }
    
    public function AssertMultipleTrue($object, $method, array $params)
    {
        foreach($params as $array)
            $this ->AssertTrue ($object, $method, $array);
    }
    
    public function AssertMultipleFalse($object, $method, array $params)
    {
        foreach($params as $array)
            $this ->AssertFalse ($object, $method, $array);
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
          echo $this ->linebreak(1).$this ->green('Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']) ;
        } 
        else 
        {
          echo 'Curl error: ' . curl_error($tuCurl);
        }
        
        curl_close($tuCurl);
        
        return $tuData;
    }
    
    public function ShowResults() {

        echo $this ->linebreak(2);
        echo 'Passed: ',$this ->green(self::$passed) , ', Failed: ',$this -> red(self::$failed) , ', Assertions: ', $this -> blue(self::$assertions) ,'.', $this ->linebreak(2);
    }
    
    public function AssertType($data, $type)
    {
        self::$assertions +=1;

        $with = ' with AssertType();';
        $passed = $this->green('Test type '.$type.' passed'.$with).$this->linebreak(1);
        $failed = $this->red('Test type '.$type.' failed in class '. get_called_class().$with).$this->linebreak(1);
        
        $this ->updateResult($this ->CheckType($data, $type), $passed, $failed);
    }
    
    public function CheckType($data, $type)
    {
        
            if(strtolower($type) == 'integer')
            {
                if(is_int($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'number')
            {
                if(is_numeric($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'string')
            {
                if(is_string($data) AND !empty($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'json')
            {
                if(json_decode($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'array')
            {
                if(is_array($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'object')
            {
                if(is_object($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'boolean')
            {
                echo $data;
                if(is_bool($data) || $data == false)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'char')
            {
                if(sizeof($data) == 1)
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'float')
            {
                if(is_float($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            else if(strtolower($type) == 'notNull')
            {
                if(!is_null($data))
                {
                    $pass = 1;
                }
                else
                {
                    $pass = 0;
                }
            }
            
            return $pass;
    }
}