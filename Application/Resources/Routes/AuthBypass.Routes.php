<?php

Set::Config('AuthBypassRoutes', array(

    'Application',
    \Get::Config('Application.LandingPageRoute'),
    \Get::Config('Auth.Login.LoginRoute'),
    \Get::Config('Auth.Login.BeforeLogoutHookRoute'),
    \Get::Config('Auth.Login.LoginAuthRoute'),
    \Get::Config('Auth.Login.LoggedOutDefaultRoute'),
));