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
$app = $loader->LoadGenesis();
AppKernal::Initialize();
$router = $app->getComponent('Router');
$loader->loadBundleConfigs();

if(! $router->ForwardRequest())
{
    $router->ForwardToController('404', array('pattern'=> $router->GetPattern()));
}