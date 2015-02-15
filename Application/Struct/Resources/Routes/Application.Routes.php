<?php

use Application\Components\Router\Router;

Router::Add('Application', array(
    'Controller' => ':Application:index',
    'Pattern' => '/',
));
