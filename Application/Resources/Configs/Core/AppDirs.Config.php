<?php



// Core folder paths

define('HOST', 'http://localhost/GENESIS/');
define('ROOT', __DIR__ . '/../../../../');

\Set::Config('CORE', array(

    'APPLICATION_FOLDER' => ROOT . 'Application/',
    'APPLICATION_CONSOLE_FOLDER' => ROOT . 'Application/Console/',
    'APPLICATION_CLASSES_FOLDER' => ROOT . 'Application/Classes/',
    'APPLICATION_COMPONENTS_FOLDER' => ROOT . 'Application/Classes/Components/',
    'APPLICATION_RESOURCES_FOLDER' => ROOT . 'Application/Resources/',
    'APPLICATION_MODELS_FOLDER' => ROOT . 'Application/Model/',
    'APPLICATION_CONFIGS_FOLDER' => ROOT . 'Application/Resources/Configs/',
    'APPLICATION_ROUTES_FOLDER' => ROOT . 'Application/Resources/Routes/',
    'APPLICATION_CONTROLLERS_FOLDER' => ROOT . 'Application/Controllers/',
    'APPLICATION_TESTS_FOLDER' => ROOT . 'Application/Tests/',
    'SOURCE_FOLDER' => ROOT . 'Source/',
    'BUNDLES_FOLDER' => ROOT . 'Source/Bundles/',
    'CACHE_FOLDER' => ROOT . 'Public/Cache',

    'TEMPLATING' => array(
        'TEMPLATES_FOLDER' => ROOT . 'Application/Resources/Views/',
        'ERRORS_TEMPLATES_FOLDER' => ROOT . 'Application/Resources/Views/Error_Pages/',
        'PUBLIC_FOLDER' => HOST . 'Public/',
        'ASSETS_FOLDER' => HOST . 'Public/Assets/',
        'IMAGES_FOLDER' => HOST . 'Public/Assets/Images/',
        'CSS_FOLDER' => HOST . 'Public/Assets/CSS/',
        'JS_FOLDER' => HOST . 'Public/Assets/JS/'
    ),

    'BUNDLES' => array(
        'BUNDLE_CONFIGS' => '/Resources/Configs/',
        'BUNDLE_ASSETS_FOLDER' => ROOT . 'Public/Assets/Bundles/',
        'BUNDLE_DATABASE_FILES' => '/Model/',
        'BUNDLE_INTERFACES' => '/Interfaces/',
        'BUNDLE_CONTROLLERS' => '/Controllers/',
        'BUNDLE_ROUTES' => '/Resources/Routes/',
        'BUNDLE_VIEWS' => '/Resources/Views/',
        'BUNDLE_EVENTS' => '/Events/',
        'BUNDLE_TESTS' => '/Tests/',
        'BUNDLE_VIEW_HEADER_FILE' => 'Header.html.php',
        'BUNDLE_VIEW_FOOTER_FILE' => 'Footer.html.php',
    )
));