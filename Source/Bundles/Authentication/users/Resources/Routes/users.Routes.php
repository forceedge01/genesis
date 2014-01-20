<?php


Set::Route('users', array(

    'Controller' => 'users:users:index',
    'Pattern' => '/users/'
));

Set::Route('users_login', array(

    'Controller' => 'users:users:login',
    'Pattern' => '/login/',
    'Method' => 'get',
));

Set::Route('users_logout', array(

    'Controller' => 'users:users:logout',
    'Pattern' => '/logout/'
));

Set::Route('users_login_auth', array(

    'Controller' => 'users:users:loginAuth',
    'Pattern' => '/loginAuth/',
    'Method' => array(
        'Type' => 'post',
        'Message' => 'Unable to login',
        'Fallback' => 'users_login'
    ),
));

Set::Route('users_List', array(

    'Controller' => 'users:users:list',
    'Pattern' => '/users/List/'
));

Set::Route('users_Create', array(

    'Controller' => 'users:users:create',
    'Pattern' => '/users/create/'
));