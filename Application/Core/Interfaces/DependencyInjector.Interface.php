<?php

namespace Application\Core\Interfaces;



interface DependencyInjector {

    public function __construct() ;
    
    public function Inject($dependent, array $dependencies);

    public function ResolveDependencies(array $dependencies);
}