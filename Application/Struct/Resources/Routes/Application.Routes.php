<?php

use Router\Router;

Router::Add('Application', array(
    'Controller' => ':Application:index',
    'Pattern' => '/',
));