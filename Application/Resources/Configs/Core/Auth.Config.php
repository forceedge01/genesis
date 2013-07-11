<?php

Set::Config('Auth', array(

    'Form' => array(

        'EmailFieldName' => 'username',
        'PasswordFieldName' => 'password',
    ),
    
    'Validation' => array(

        'Email' => array(
            
            'Enable' =>true,
            'Message' => 'Invalid characters found in email address'
        ),
    ),
    
    'DBTable' => array(

        'AuthTableName' => 'users',
        'AuthColumnName' => 'email',
    ),
    
    'Security' => array(

        'SessionInterval' => 60*60, // 1
        'BruteForce' => array(
            
            'MaxLoginAttempts' => 3,
            'Message' => 'Your account has been locked for trying too many times, try again later',
            'BlockedCoolDownPeriod' => 10, // 1
        ),
        'Salt' => 'kjahsdjkfhlasjkdfhlajkshdfkjashlfjkhs', // 2
        'PasswordEncryption' => 'SHA512', // 3
        'Bypass' => array(

            '^/$',
            '^/login/$',
            '^/loginAuth/$'
        )
    ),
    
    'Login' => array(

        'EntityRepository' => '\\Bundles\\users\\Entities\\usersEntity',
        'UserPopulateMethod' => false,

        'LoginRoute' => 'users_login',
        'LoginAuthRoute' => 'users_login_auth',
        'LoggedInDefaultRoute' => 'Application',

        'LoggedOutDefaultRoute' => 'users_login',
        'BeforeLogoutHookRoute' => false, // 4
        'AfterLogoutHookRoute' => false, // 5
    ),
));

/*
 * Legend
 *
 * 1. In seconds
 * 2. Used for encryption of password generation and authentication
 * 3. Used for password encryption and authentication
 * 4. User defined method fired before user is logged out
 * 5. User defined method fired after user is logged out
 */