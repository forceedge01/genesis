<?php

namespace Application\Struct\Interfaces\Controllers;



interface ErrorController{

    function RouteNotFoundAction($route, $pattern, $backtrace);

    function ActionNotFoundAction($action, $class, $controller, $route, $backtrace);

    function ClassNotFoundAction($action, $class, $controller, $route, $backtrace);

    function NotFoundError404Action();

    function ServerError500Action();
}