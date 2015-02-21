<?php

class Get{

    public function __construct() {}
    private function __clone() {}

    /**
     *
     * @param mixed any number of params
     * @return Configuration variable
     */
    public static function Config()
    {
        $keys = func_get_args();
        $config = self::ProcessGet(Application\Loader::$appConfiguration, $keys);

        if(is_array($config))
        {
            $newArray = array();

            foreach($config as $key => $conf)
            {
                $newArray[$key] = self::placeConfig($conf);
            }

            return $newArray;
        }
        else
        {
            return self::placeConfig($config);
        }
    }

    private static function placeConfig($config)
    {
        if(!is_array($config) and !empty($config))
        {
            $matches = array();

            if(preg_match_all('/({{.+?}})/', $config, $matches))
            {
                if(count($matches[0]))
                {
                    foreach($matches[0] as $match)
                    {
                        $config = str_replace($match, self::Config (trim($match, '{}')), $config);
                    }
                }
            }

            if($config === null)
            {
                die('<pre>Key '.print_r($config, true).' not found</b><br /><br /><pre>'.print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));
            }

            if(strpos($config, '{{') !== false)
                $config = self::placeConfig ($config);
        }

        return $config;
    }

    public static function ProcessGet($globalVariable, $keys)
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
