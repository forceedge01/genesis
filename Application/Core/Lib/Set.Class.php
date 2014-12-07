<?php


class Set {

    public function __construct() {}
    private function __clone() {}

    /**
     *
     * @param string $key
     * @param mixed $config
     */
    public static function Config($key, $config)
    {
        \Application\Loader::$appConfiguration[$key] = $config;
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

        \Application\Loader::$appConfiguration = array_replace_recursive(self::$appConfiguration, $result);
    }

    public static function Component($alias, $class)
    {
        \Application\Loader::$components[$alias] = $class;
    }
}