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
        'Session' => array(
            'Interval' => 60*60, // 1
            'ExpireMessage' => 'Your session has expired, please login again.',
            'BruteForce' => array(
                'MaxLoginAttempts' => 5,
                'Message' => 'Your account has been locked for trying too many times, try again later',
                'BlockedCoolDownPeriod' => 1, // 1
                'MailUserOnBlock' => array(
                    'Enabled' => false,
                    'Body' => 'Your account has been blocked due to too many login attempts, please check with us as soon as possible.',
                    'Subject' => 'Your account has been blocked.'
                ), // 6
            ),
            'Anti-Hijacking' => array(
                'Message' => 'For security reasons you have been logged out, please login again.'
            )
        ),

        'Salt' => 'kjahsdjkfhlasjkdfhlajkshdfkjashlfjkhs', // 2
        'PasswordEncryption' => 'SHA512', // 3
        'Bypass' => array(
            '^/$',
            '^/login/$',
            '^/loginAuth/$'
        ),
        'AccessDeniedMessage' => 'You need to login to access this page.'
    ),

    'Login' => array(
        'EntityRepository' => '\\Bundles\\users\\Entities\\usersEntity',
        'UserPopulateMethod' => false,

        'LoginRoute' => 'users_login',
        'LoginAuthRoute' => 'users_login_auth',
        'LoggedInDefaultRoute' => 'users_List',

        'LoggedOutDefaultRoute' => 'users_login',
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
 * 6. User will be emailed on blocking there account, the username field will be taken as the email
 */