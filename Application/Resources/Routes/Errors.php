<?php

Router::$Route['Error_Route_Not_Found'] = array(

    'Controller' => 'Errors:RouteNotFound',
    'Pattern' => '/RouteNotFound/'

);

Router::$Route['Class_Not_Found'] = array(

    'Controller' => 'Errors:ClassNotFound',
    'Pattern' => '/ClassNotFound/'

);

Router::$Route['Action_Not_Found'] = array(

    'Controller' => 'Errors:ActionNotFound',
    'Pattern' => '/ActionNotFound/'

);

Router::$Route['404'] = array(

    'Controller' => 'Errors:NotFoundError404',
    'Pattern' => '/404/'

);

Router::$Route['500'] = array(

    'Controller' => 'Errors:ServerError500',
    'Pattern' => '/500/'

);