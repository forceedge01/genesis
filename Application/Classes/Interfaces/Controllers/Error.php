<?php

namespace Application\Interfaces\Controllers;



interface Error{

    function RouteNotFoundAction($route, $pattern, $backtrace);

    function ActionNotFoundAction($action, $class, $controller, $route, $backtrace);

    function ClassNotFoundAction($action, $class, $controller, $route, $backtrace);

    function NotFoundError404Action();

    function ServerError500Action();
}