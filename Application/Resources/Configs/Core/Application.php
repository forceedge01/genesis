<?php

Set::Config('Application', array(
    
    'Name' => 'Project Genesis NS 0.2',
    'Admin_Email' => 'wahab.qureshi@digitalanimal.com',
    'Base_Route_Name' => 'Application',
    'Session' => array(
        
        'Enabled' => false,
        'Interval' => 60*60,
        'Login_Route_Name' => 'Login_Home',
    )
));

define('APPLICATION_NAME', 'Project Genesis NS 0.2');

define('SESSION_ENABLED', false);

define('SESSION_TIME_INTERVAL', 60 * 60); //in seconds
//
//Setup for correct redirection, do not delete these paths, update accordingly or application will endup in a loop back.
define('LOGIN_ROUTE_NAME', 'Login_Home');

define('LOGIN_AUTH_ROUTE_NAME', 'Login_Auth');

define('APPLICATION_BASE_ROUTE_NAME', 'Application');

define('APPLICATION_ADMIN_EMAIL', 'wahab.qureshi@digitalanimal.com');