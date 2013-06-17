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

        $configs = array();

        foreach($keys as $key)
        {
            $configs[$key] = self::GetGlobal(Application\Core\Loader::$appConfiguration, $key);
        }

        if(count($configs) == 1)
        {
            reset($configs);
            return $configs[key($configs)];
        }
        else
            return $configs;
    }

    /**
     *
     * @param mixed any number of params
     * @return Route variable
     */
    public static function Route()
    {
        $keys = func_get_args();

        $configs = array();

        foreach($keys as $key)
        {
            $configs[$key] = self::GetGlobal(Application\Core\Router::$Route, $key);
        }

        if(count($configs) == 1)
        {
            reset($configs);
            return $configs[key($configs)];
        }
        else
            return $configs;
    }

    private static function GetGlobal($config, $key)
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
                    echo '<pre>Key '.$index.' not found as defined by <b>'.$key.'</b><br /><br />';
                    print_r(debug_backtrace(1, 3)[1]);
                    exit;
                }
            }

            return $config;
        }
        else
            return $config[$key];
    }
}
