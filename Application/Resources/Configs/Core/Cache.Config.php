<?php

Set::Config('Cache', array(

    'html' => array(

        'enabled' => false,
        'minify' => true,
        'compress' => array(
            'enabled' => false,
            'level' => 5
        ),
        'expire' => 60*60,
    ),
    'javascript' => array(

        'enabled' => true,
        'minify' => false,
        'unify' => true
    ),
    'css' => array(

        'enabled' => true,
        'minify' => true,
        'unify' => false
    ),
));

// Minify: Will remove white spaces and possibly comments from the files
// Unify: Will merge filetypes as one file, reducing the number of http requests to the server