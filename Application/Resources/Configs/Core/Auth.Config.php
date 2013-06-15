<?php

Set::Config('Auth', array(

    'Form' => array(

        'EmailFieldName' => 'username',
        'PasswordFieldName' => 'password',
    ),
    'Validation' => array(

        'Email' => true,
    ),
    'DBTable' => array(

        'AuthTableName' => 'users',
        'AuthColumnName' => 'email',
    ),
    'Security' => array(

        'Interval' => 60*60,
        'MaxLoginAttempts' => 3,
        'BlockedCoolDownPeriod' => 10,
        'Salt' => 'kjahsdjkfhlasjkdfhlajkshdfkjashlfjkhs',
        'PasswordEncryption' => 'SHA512',
    ),
    'Login' => array(

        'EntityRepository' => '\\Application\\Bundles\\users\\Entities\\usersEntity',
        'UserPopulateMethod' => 'populateUser',
        'LoginRoute' => 'users_login',
        'LoggedOutDefaultRoute' => 'users_login',
        'LogoutHookRoute' => false,
        'LoginAuthRoute' => 'users_login_auth',
        'LoggedInDefaultRoute' => 'Application',
    ),
));