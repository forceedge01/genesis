<?php


class Set extends Application\Core\Loader{

    private function __construct() {}
    private function __clone() {}

    /**
     *
     * @param string $key
     * @param mixed $config
     */
    public static function Config($key, $config)
    {
        self::$appConfiguration[$key] = $config;
    }

    /**
     *
     * @param string $key
     * @param array $routeParams
     */
    public static function Route($key, array $routeParams)
    {
        Application\Core\Router::$Route[$key] = $routeParams;
    }

    public static function OverwriteConfig($stringKey, $value)
    {
        $result = array();

        $keys = strpos($stringKey, '.') !== false ? explode('.', $stringKey) : array($stringKey);
        $ptr = &$result;

        foreach ($keys as $key)
        {
            if (!isset($ptr[$key]))
            {
                $ptr[$key] = array();
            }
            
            $ptr = &$ptr[$key];
        }

        if (empty($ptr))
        {
            $ptr = $value;
        }
        else
        {
            $ptr[] = $value;
        }

        self::$appConfiguration = array_replace_recursive(self::$appConfiguration, $result);
    }
}