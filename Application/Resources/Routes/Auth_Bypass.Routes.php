<?php

Set::Config('AUTH_BYPASS_ROUTES', array(

    \Get::Config('Auth.LoginRoute'),
    \Get::Config('Auth.LogoutRoute'),
    \Get::Config('Auth.LoginAuthRoute'),
    \Get::Config('Auth.LoggedOutDefaultRoute'),
));