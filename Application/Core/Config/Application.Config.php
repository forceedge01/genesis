<?php

Set::Config('Application', array(

    'Name' => 'Project Genesis NS 0.2',
    'Admin_Email' => 'wahab.qureshi@digitalanimal.com',
    'HomeRoute' => 'users_login',
    'LandingPageRoute' => 'users_login',
    'Session' => array(
        'Enabled' => true,
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