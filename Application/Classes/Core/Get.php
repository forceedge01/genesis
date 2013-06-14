<?php

class Get{
    
    /**
     * 
     * @param mixed any number of params
     * @return Configuration variable
     */
    public static function Config()
    {
        $keys = func_get_args();
        
        return self::GetGlobal(Application\Core\Loader::$appConfiguration, $keys);
    }
    
    /**
     * 
     * @param mixed any number of params
     * @return Route variable
     */
    public static function Route()
    {   
        $keys = func_get_args();
        
        return self::GetGlobal(Application\Core\Router::$Route, $keys);
    }
    
    private static function GetGlobal($config, $keys)
    {        
        $configs = array();
        
        foreach($keys as $key)
        {
            $keyIndexes = explode('.', $key);
            
            if(is_array($keyIndexes))
            {
                foreach ($keyIndexes as $index) 
                {
                    if (isset($config[$index]))
                    {
                        $config = $config[$index];
                    }
                    else 
                    {
                        return null;
                    }
                }

                $configs[] = $config;
            }
            else
                $configs[$route] = Application\Core\Router::$Route[$route];
        }
        
        if(count($configs) == 1 && array_key_exists('0', $configs))
            return $configs[0];
        else
            return $configs;
    }
}
