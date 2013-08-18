<?php

namespace Application\Core;



class DependencyInjector extends AppMethods{

    public function __construct() {}

    public function Inject($injectSubject, array $injectObject)
    {
        $reflection = new \ReflectionClass($injectSubject);

        if(!$this->IsLoopable($injectObject))
            return $reflection->newInstance ();

        $dependencies = array();

        foreach($injectObject as $inject)
        {
            $dependency = $this->ExplodeAndGetLastChunk($inject, '\\');
            $dependencies[] = $this->GetObject($dependency);

            if(!end($dependencies))
                $this->SetErrorArgs('Unable to inject dependency: '.$inject.' for object '. get_class($injectObject) . ', make sure the class exists', __FILE__, __LINE__)->ThrowError();
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}