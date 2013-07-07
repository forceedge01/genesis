<?php


class Set extends Application\Core\Loader{

    /**
     *
     * @param string $key
     * @param mixed $config
     */
    public static function Config($key, $config)
    {
        Application\Core\Loader::$appConfiguration[$key] = $config;
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
}