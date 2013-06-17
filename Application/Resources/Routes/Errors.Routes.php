<?php

Set::Route('Error_Route_Not_Found', array(

    'Controller' => ':Errors:RouteNotFound',
    'Pattern' => '/RouteNotFound/'

));

Set::Route('Class_Not_Found', array(

    'Controller' => ':Errors:ClassNotFound',
    'Pattern' => '/ClassNotFound/'

));

Set::Route('Action_Not_Found', array(

    'Controller' => ':Errors:ActionNotFound',
    'Pattern' => '/ActionNotFound/'

));

Set::Route('404', array(

    'Controller' => ':Errors:NotFoundError404',
    'Pattern' => '/404/'

));

Set::Route('500', array(

    'Controller' => ':Errors:ServerError500',
    'Pattern' => '/500/'

));