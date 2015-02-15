<?php

use Application\Components\Router\Router;


Router::Add('Route_Not_Found', array(

    'Controller' => ':Errors:RouteNotFound',
    'Pattern' => '/RouteNotFound/'

));

Router::Add('Class_Not_Found', array(

    'Controller' => ':Errors:ClassNotFound',
    'Pattern' => '/ClassNotFound/',

));

Router::Add('Action_Not_Found', array(

    'Controller' => ':Errors:ActionNotFound',
    'Pattern' => '/ActionNotFound/',

));

Router::Add('Template_Not_Found', array(

    'Controller' => ':Errors:TemplateNotFound',
    'Pattern' => '/TemplateNotFound/',

));

Router::Add('404', array(

    'Controller' => ':Errors:NotFoundError404',
    'Pattern' => '/404/'

));

Router::Add('500', array(

    'Controller' => ':Errors:ServerError500',
    'Pattern' => '/500/'

));
