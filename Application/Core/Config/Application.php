<?php

Set::Config('Application', array(

    'Name' => 'Genesis',
    'Admin_Email' => 'its.inevitable@hotmail.com',
    'HomeRoute' => 'users_login',
    'LandingPageRoute' => 'users_login',
    'Session' => array(
        'Enabled' => false,
        'Secure' => array(
            'HttpsSecure' => false,
            'HttpOnly' => true,
        ),
        'UseAuthComponent' => true
    ),
    'Environment' => array(

        'State' => 'development',
        'UnderDevelopmentPage' => array(

            'State' => false,
	    'Controller' => ':Application:UnderDevelopment',
            'ExemptIPs' => array(

                '::',
            ),
        )

    ),
));

Set::Config('templateHandler', [
    'component' => 'Template',
]);

Set::Config('routeHandler', [
    'component' => 'Router',
]);

Set::Config('requestHandler', [
    'component' => 'Request'
]);

Set::Config('sessionHandler', [
    'component' => 'Session'
]);