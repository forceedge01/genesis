<?php

Set::Config('Cache', array(

    'html' => array(

        'enabled' => false,
        'minify' => true,
        'compress' => true,
        'expire' => 60*60,
        'folder' => ROOT . 'Public/Cache/',
    ),
    'javascript' => array(

        'enabled' => true,
        'minify' => true,
        'compress' => false,
        'expire' => 60*60,
        'unify' => false
    ),
    'css' => array(

        'enabled' => false,
        'minify' => false,
        'compress' => false,
        'expire' => 60*60,
        'unify' => false
    )
));