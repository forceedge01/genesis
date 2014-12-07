<?php

namespace Application\Core\Interfaces;



interface DependencyInjector {

    public function __construct() ;

    /**
     *
     * @param type $injectSubject
     * @param array $injectObject
     * @return type
     */
    public function Inject($dependent, array $dependencies);

    public function ResolveDependencies(array $dependencies);
}