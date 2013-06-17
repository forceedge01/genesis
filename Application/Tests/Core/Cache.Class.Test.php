<?php

namespace Application\Core\Tests;


use Application\Console\BaseTestingRoutine as base;

class CacheTest extends base{
    
    public function testMethodCheckCacheFile()
    {
        self::$testClass = new \Application\Core\Cache();
        
        $method = 'CheckForCachedFile';
        
        $this ->AssertNumberOfMethodArguments($method, 1);
        $this ->AssertArgumentParameterForMethod($method, 'pattern');
        $this ->AssertMultipleFalse($method, array(
            'test1' => array(
                'parameters' => array(),
                'case' => 'equals',
                'expected' => 'abc',
            ),
        ));
    }
    
    public function testMethodWriteCacheFile()
    {
        $method = 'WriteCacheFile';
    }
    
    public function testMethodReadCacheFile()
    {
        $method = 'ReadCacheFile';
    }
    
    public function testMethodOutputCacheFile()
    {
        $method = 'OutputCacheFile';
    }
}