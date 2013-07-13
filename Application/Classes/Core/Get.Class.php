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
        $config = self::ProcessGet(Application\Core\Loader::$appConfiguration, $keys);

        if($config === null)
        {
            die('<pre>Key '.print_r($keys, true).' not found</b><br /><br /><pre>'.print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));
        }

        return $config;
    }

    /**
     *
     * @param mixed any number of params
     * @return Route variable
     * @example Route('Application','Welcome');
     */
    public static function Route()
    {
        $keys = func_get_args();

        return self::ProcessGet(Application\Core\Router::$Route, $keys);
    }

    private static function ProcessGet($globalVariable, $keys)
    {
        $configs = array();

        foreach($keys as $key)
        {
            $configs[$key] = self::GetGlobal($globalVariable, $key);
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
                    return false;
                }
            }

            return $config;
        }
        else
            return $config[$key];
    }
}
