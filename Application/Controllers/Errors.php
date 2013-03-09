<?php

class ErrorsController extends Application{

    public function RouteNotFoundAction($route, $pattern, $backtrace){

        $params = null;

        $params['PageTitle'] = 'Route Not Found!';

        $params['Error'] = array(

            'Route' => $route,
            'Pattern' => $pattern,
            'Backtrace' => $backtrace
        );

        $this->Render('Bundle:Errors:Route_Not_Found.html.php', $params);
    }

    public function ActionNotFoundAction($action, $class, $controller, $route, $backtrace){

        $params = null;

        $params['PageTitle'] = 'Action Not Found!';

        $params['Error'] = array(

            'Action' => $action,
            'Class' => $class,
            'Controller' => $controller,
            'Route' => $route,
            'Backtrace' => $backtrace
        );

        $this->Render('Bundle:Errors:Action_Not_Found.html.php', $params);
    }

    public function ClassNotFoundAction($action, $class, $controller, $route, $backtrace){

        $params = null;

        $params['PageTitle'] = 'Class Not Found!';

        $params['Error'] = array(

            'Action' => $action,
            'Class' => $class,
            'Controller' => $controller,
            'Route' => $route,
            'Backtrace' => $backtrace
        );

        $this->Render('Bundle:Errors:Class_Not_Found.html.php', $params);
    }
}