<?php

namespace Application\Controllers;



use \Application\Core\Application;

use Application\Interfaces\Controllers\Error;


class ErrorsController extends Application implements Error{

    public function RouteNotFoundAction($route, $pattern, $backtrace){

        $params = null;

        $params['Error'] = array(

            'Route' => $route,
            'Pattern' => $pattern,
            'Backtrace' => $backtrace
        );

        $this ->GetResponseManager() ->SetNotFound();

        $this->Render(':Errors:RouteNotFound.html.php', 'Route Not Found!', $params);
    }

    public function ActionNotFoundAction($action, $class, $controller, $route, $backtrace){

        $params = null;

        $params['Error'] = array(

            'Action' => $action,
            'Class' => $class,
            'Controller' => $controller,
            'Route' => $route,
            'Backtrace' => $backtrace
        );

        $this ->GetResponseManager() ->SetStatus(400);

        $this->Render(':Errors:ActionNotFound.html.php', 'Action Not Found!', $params);
    }

    public function ClassNotFoundAction($action, $class, $controller, $route, $backtrace){

        $params = null;

        $params['Error'] = array(

            'Action' => $action,
            'Class' => $class,
            'Controller' => $controller,
            'Route' => $route,
            'Backtrace' => $backtrace
        );

        $this ->GetResponseManager() ->SetStatus(400);

        $this->Render(':Errors:ClassNotFound.html.php', 'Class Not Found!', $params);
    }

    public function TemplateNotFoundAction($template){

        $this ->GetResponseManager() ->SetBadRequest();

        $this->Render(':Errors:TemplateNotFound.html.php', 'Template Not Found!', $template);
    }

    public function NotFoundError404Action(){

        $this->GetResponseManager()->SetNotFound();
        $this->Render(':Errors:error404.html.php', 'Error 404: Page not found');
    }

    public function ServerError500Action(){

        $this->GetResponseManager()->SetInternalServerError();
        $this->Render(':Errors:error500.html.php', 'Error 500: Internal server error');
    }
}