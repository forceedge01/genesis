<?php

define('AUTH_EMAIL_FIELD_NAME', 'email');//POST request

define('AUTH_PASSWORD_FIELD_NAME', 'password');//POST request

define('AUTH_VALIDATE_USERNAME_IF_EMAIL', true);

define('AUTH_TABLE_NAME', 'Users');

define('AUTH_FIELD_IN_TABLE_NAME', 'email');

define('AUTH_USER_ENTITY' , 'User');

define('AUTH_USER_POPULATE_METHOD', 'populateUser');

define('AUTH_PASSWORD_ENCRYPTION_ALGORITHM', 'SHA512');

define('AUTH_LOGIN_ROUTE', 'Login_Home');