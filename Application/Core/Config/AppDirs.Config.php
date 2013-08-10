<?php



// Core folder paths

define('HOST', 'http://localhost:8765/');
define('ROOT', __DIR__ . '/../../../');

\Set::Config('APPDIRS', array(

    'APPLICATION_FOLDER' => ROOT . 'Application/',
    'SOURCE_FOLDER' => ROOT . 'Source/',
    'CACHE_FOLDER' => ROOT . 'Public/Cache/',

    'APPLICATION_TESTS_FOLDER' => '{{APPDIRS.APPLICATION_FOLDER}}Tests/',
    'TRAITS_FOLDER' => '{{APPDIRS.APPLICATION_FOLDER}}Traits',

    'CORE' => array(
        'BASE_FOLDER' => '{{APPDIRS.APPLICATION_FOLDER}}Core/',
        'LIB_FOLDER' => '{{APPDIRS.CORE.BASE_FOLDER}}Lib/',
        'CONFIG_FOLDER' => '{{APPDIRS.CORE.BASE_FOLDER}}Config/',
        'INTERFACES_FOLDER' => '{{APPDIRS.CORE.BASE_FOLDER}}Interfaces/',
    ),
    'COMPONENTS' => array(
        'BASE_FOLDER' => '{{APPDIRS.APPLICATION_FOLDER}}Components/',
    ),

    'STRUCT' => array(
        'BASE_FOLDER' => '{{APPDIRS.APPLICATION_FOLDER}}Struct/',
        'RESOURCES_FOLDER' => '{{APPDIRS.STRUCT.BASE_FOLDER}}Resources/',
        'MODELS_FOLDER' => '{{APPDIRS.STRUCT.BASE_FOLDER}}Model/',
        'INTERFACES_FOLDER' => '{{APPDIRS.STRUCT.BASE_FOLDER}}Interfaces/',
        'EVENTS_FOLDER' => '{{APPDIRS.STRUCT.BASE_FOLDER}}Events/',
        'CONFIGS_FOLDER' => '{{APPDIRS.STRUCT.RESOURCES_FOLDER}}Config/',
        'ROUTES_FOLDER' => '{{APPDIRS.STRUCT.RESOURCES_FOLDER}}Routes/',
        'CONTROLLERS_FOLDER' => '{{APPDIRS.STRUCT.BASE_FOLDER}}Controllers/',
    ),

    'CONSOLE' => array(
        'BASE_FOLDER' => '{{APPLICATION_FOLDER}}Console/',
    ),

    'TEMPLATING' => array(
        'TEMPLATES_FOLDER' => ROOT . 'Application/Struct/Resources/Views/',
        'ERRORS_TEMPLATES_FOLDER' => ROOT . 'Application/Struct/Resources/Views/Error_Pages/',
        'PUBLIC_FOLDER' => HOST . 'Public/',
        'ASSETS_FOLDER' => HOST . 'Public/Assets/',
        'IMAGES_FOLDER' => HOST . 'Public/Assets/Images/',
        'CSS_FOLDER' => HOST . 'Public/Assets/CSS/',
        'JS_FOLDER' => HOST . 'Public/Assets/JS/'
    ),

    'BUNDLES' => array(
        'BASE_FOLDER' => '{{APPDIRS.SOURCE_FOLDER}}/Bundles/',
        'CONFIG' => '/Resources/Config/',
        'ASSETS_FOLDER' => ROOT . 'Public/Assets/Bundles/',
        'DATABASE_FILES' => '/Model/',
        'INTERFACES' => '/Interfaces/',
        'CONTROLLERS' => '/Controllers/',
        'ROUTES' => '/Resources/Routes/',
        'VIEWS' => '/Resources/Views/',
        'EVENTS' => '/Events/',
        'TESTS' => '/Tests/',
        'BUNDLE_VIEW_HEADER_FILE' => 'Header.html.php',
        'BUNDLE_VIEW_FOOTER_FILE' => 'Footer.html.php',
    )
));