<?php

Set::Config('Application', array(

    'Name' => 'Project Genesis NS 0.2',
    'Admin_Email' => 'wahab.qureshi@digitalanimal.com',
    'Base_Route_Name' => 'Application',
    'LandingPageRoute' => 'users_List',
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