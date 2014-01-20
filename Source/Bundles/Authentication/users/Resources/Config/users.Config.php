<?php

Set::Config('users', array(
    'Path' => '{{APPDIRS.BUNDLES.BASE_FOLDER}}users',
    'Observers' => array(

        '\\Bundles\\Authentication\\users\\Model\\usersModel',
        '\\Bundles\\Accounts\\Controllers\\AccountsController'
    ),
    'users' => array(

        'Dependencies' => array(
            'Request:Core',
            'Response',
        ),

        'login' => array(
            'Dependencies' => array(
                'HTMLGenerator:Component'
            )
        )
    )
));