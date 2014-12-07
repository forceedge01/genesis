<?php

namespace Application\Core;



use Application\Core\Interfaces\DependencyInjector as DependencyInjectorInterface;


class DependencyInjector extends AppMethods implements DependencyInjectorInterface{

    public function __construct() {}

    /**
     *
     * @param type $injectSubject
     * @param array $injectObject
     * @return type
     */
    public function Inject($dependent, array $dependencies)
    {
        $reflection = new \ReflectionClass($dependent);

        if(!$this->IsLoopable($dependencies))
            return $reflection->newInstance ();

        $dependencies = $this->ResolveDependencies($dependencies);

        return $reflection->newInstanceArgs($dependencies);
    }

    public function ResolveDependencies(array $dependencies)
    {
        if(!$this->IsLoopable($dependencies))
            return array();

        $dependenciesObjects = array();

        foreach($dependencies as $inject)
        {
            $inject = strtolower($inject);
            if(strpos($inject, 'component:') === 0)
            {
                $class = str_replace('component:', '', $inject);
                $dependenciesObjects[] = $this->GetComponent($class);

                continue;
            }

            $dependenciesObjects[] = $this->GetCoreObject($inject);
        }

        return $dependenciesObjects;
    }
}