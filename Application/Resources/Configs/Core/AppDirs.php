<?php



// Core folder paths

define('HOST', 'http://localhost/GENESIS/');

define('ROOT', __DIR__ . '/../../../../');

define('APPLICATION_FOLDER', ROOT . 'Application/');

define('APPLICATION_CONSOLE_FOLDER', APPLICATION_FOLDER . 'Console/');

define('APPLICATION_CLASSES_FOLDER', APPLICATION_FOLDER . 'Classes/');

define('APPLICATION_COMPONENTS_FOLDER', APPLICATION_CLASSES_FOLDER . 'Components/');

define('APPLICATION_RESOURCES_FOLDER', APPLICATION_FOLDER . 'Resources/');

define('APPLICATION_MODELS_FOLDER', APPLICATION_FOLDER . 'Model/');

define('APPLICATION_CONFIGS_FOLDER', APPLICATION_RESOURCES_FOLDER . 'Configs/');

define('APPLICATION_ROUTES_FOLDER', APPLICATION_RESOURCES_FOLDER . 'Routes/');

define('APPLICATION_CONTROLLERS_FOLDER', APPLICATION_FOLDER . 'Controllers/');

define('APPLICATION_TESTS_FOLDER', APPLICATION_FOLDER . 'Tests/');

define('SOURCE_FOLDER', ROOT . 'Source/');

define('BUNDLES_FOLDER', SOURCE_FOLDER . 'Bundles/');



// Templating and public folders

define('TEMPLATES_FOLDER', APPLICATION_RESOURCES_FOLDER . 'Views/');

define('ERRORS_TEMPLATES_FOLDER', TEMPLATES_FOLDER . 'Error_Pages/');

define('PUBLIC_FOLDER', HOST . 'Public/');

define('ASSETS_FOLDER', PUBLIC_FOLDER . 'Assets/');

define('IMAGES_FOLDER', ASSETS_FOLDER . 'Images/');

define('CSS_FOLDER', ASSETS_FOLDER . 'CSS/');

define('JS_FOLDER', ASSETS_FOLDER . 'JS/');

// Bundle structure

define('BUNDLE_CONFIGS', '/Resources/Configs/');

define('BUNDLE_DATABASE_FILES', '/Model/');

define('BUNDLE_INTERFACES', '/Interfaces/');

define('BUNDLE_CONTROLLERS', '/Controllers/');

define('BUNDLE_ROUTES', '/Resources/Routes/');

define('BUNDLE_VIEWS', '/Resources/Views/');

define('BUNDLE_TESTS', '/Tests/');

define('BUNDLE_VIEW_HEADER_FILE', 'Header.html.php');

define('BUNDLE_VIEW_FOOTER_FILE', 'Footer.html.php');