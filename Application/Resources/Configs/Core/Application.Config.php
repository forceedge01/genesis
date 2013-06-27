<?php

Set::Config('Application', array(

    'Name' => 'Project Genesis NS 0.2',
    'Admin_Email' => 'wahab.qureshi@digitalanimal.com',
    'Base_Route_Name' => 'Application',
    'Session' => array(

        'Enabled' => true,
        'Secure' => false,
        'HttpOnly' => true,
    ),
    'Environment' => array(

        'State' => 'development',
        'UnderDevelopmentPage' => array(

            'State' => true,
            'Controller' => ':Application:UnderDevelopment',
            'ExemptIPs' => array(

                '::1',
            ),
        )

    ),
));