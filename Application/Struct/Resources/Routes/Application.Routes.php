<?php

use Router\Router;

Router::Set('Application', array(

    'Controller' => ':Application:index',
    'Pattern' => '/{id}/something',
));