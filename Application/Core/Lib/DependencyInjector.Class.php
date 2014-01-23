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
            $dependenciesObjects[] = $this->GetObject($inject);

            if(!end($dependenciesObjects))
                $this->SetErrorArgs('Unable to inject dependency: '.$inject.' for object '. get_class($dependenciesObjects) . ', make sure the class exists', __FILE__, __LINE__)->ThrowError();
        }

        return $dependenciesObjects;
    }
}