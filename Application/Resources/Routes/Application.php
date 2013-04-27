<?php

use Application\Core\Router;



Router::$Route['Application'] = array(

    'Controller' => ':Application:index',
    'Pattern' => '/',

);