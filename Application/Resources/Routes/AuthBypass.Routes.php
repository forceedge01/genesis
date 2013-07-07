<?php

Set::Config('AuthBypassRoutes', array(

    \Get::Config('Auth.Login.LoginRoute'),
    \Get::Config('Auth.Login.BeforeLogoutHookRoute'),
    \Get::Config('Auth.Login.LoginAuthRoute'),
    \Get::Config('Auth.Login.LoggedOutDefaultRoute'),
));