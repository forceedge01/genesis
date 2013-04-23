<?php

// Core folder paths

define('HOST', 'http://localhost/GENESIS/');

define('ROOT', __DIR__ . '/../../../../');

define('APPLICATION_FOLDER', ROOT . 'Application/');

define('APPLICATION_CONSOLE_FOLDER', APPLICATION_FOLDER . 'Console/');

define('APPLICATION_CLASSES_FOLDER', APPLICATION_FOLDER . 'Classes/');

define('APPLICATION_COMPONENTS_FOLDER', APPLICATION_CLASSES_FOLDER . 'Components/');

define('APPLICATION_RESOURCES_FOLDER', APPLICATION_FOLDER . 'Resources/');

define('APPLICATION_ENTITIES_FOLDER', APPLICATION_FOLDER . 'Entities/');

define('APPLICATION_CONFIGS_FOLDER', APPLICATION_RESOURCES_FOLDER . 'Configs/');

define('APPLICATION_ROUTES_FOLDER', APPLICATION_RESOURCES_FOLDER . 'Routes/');

define('APPLICATION_CONTROLLERS_FOLDER', APPLICATION_FOLDER . 'Controllers/');

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