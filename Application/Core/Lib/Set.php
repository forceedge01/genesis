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
        \Application\Loader::$appConfiguration[$key] = isset(\Application\Loader::$appConfiguration[$key]) ? array_merge_recursive(\Application\Loader::$appConfiguration[$key], $config) : $config;
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
        if(isset(\Application\Loader::$components[$alias]))
        {
            Application\Core\Debugger::ThrowStaticError("Component '$alias' already registered");
        }

        \Application\Loader::$components[$alias] = $class;
    }

    public static function Components(array $components)
    {
        foreach($components as $component => $ref)
        {
            self::Component($component, $ref);
        }
    }
}