<?php

Set::Config('Application', array(

    'Name' => 'Project Genesis NS 0.2',
    'Admin_Email' => 'wahab.qureshi@digitalanimal.com',
    'Base_Route_Name' => 'Application',
    'LandingPageRoute' => 'users_List',
    'Environment' => 'production',
    'Session' => array(

        'Enabled' => true,
        'Secure' => false,
        'HttpOnly' => true,
    ),
    'Environment' => array(

        'State' => 'development',
        'UnderDevelopmentPage' => array(

            'State' => false,
            'Controller' => ':Application:UnderDevelopment',
            'ExemptIPs' => array(

                '::1',
            ),
        )

    ),
));