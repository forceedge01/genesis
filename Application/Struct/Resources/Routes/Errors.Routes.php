<?php

use Router\Router;


Router::Set('Route_Not_Found', array(

    'Controller' => ':Errors:RouteNotFound',
    'Pattern' => '/RouteNotFound/'

));

Router::Set('Class_Not_Found', array(

    'Controller' => ':Errors:ClassNotFound',
    'Pattern' => '/ClassNotFound/',

));

Router::Set('Action_Not_Found', array(

    'Controller' => ':Errors:ActionNotFound',
    'Pattern' => '/ActionNotFound/',

));

Router::Set('Template_Not_Found', array(

    'Controller' => ':Errors:TemplateNotFound',
    'Pattern' => '/TemplateNotFound/',

));

Router::Set('404', array(

    'Controller' => ':Errors:NotFoundError404',
    'Pattern' => '/404/'

));

Router::Set('500', array(

    'Controller' => ':Errors:ServerError500',
    'Pattern' => '/500/'

));