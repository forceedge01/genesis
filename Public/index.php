<?php

/**
 * Developer: Wahab Qureshi
 * Date: 12-02-2013
 * Version: 0.5.11-22
 */

require_once '../Application/AppKernal.php';

use Application\AppKernal;


// Initialize the kernel
$loader = AppKernal::getLoader();
// LoadBootstrap for production only
//$loader->LoadBoostrap();

// Load genesis framework
$loader->LoadGenesis();

// Initialize appkernal settings
AppKernal::Initialize();

// New app
$app = $loader->getAppInstance();

// Instantiate router instance and forward the request
$router = $app->getComponent('Router');

// Register bundle configurations
$loader->loadBundleConfigs();

if(! $router->ForwardRequest())
{
    $router->ForwardToController('404', array('pattern'=> $router->GetPattern()));
}