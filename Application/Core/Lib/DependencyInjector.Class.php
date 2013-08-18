<?php

namespace Application\Core;



class DependencyInjector extends AppMethods{

    public function __construct() {}

    /**
     *
     * @param type $injectSubject
     * @param array $injectObject
     * @return type
     */
    public function Inject($injectSubject, array $injectObject)
    {
        $reflection = new \ReflectionClass($injectSubject);

        if(!$this->IsLoopable($injectObject))
            return $reflection->newInstance ();

        $dependencies = array();

        foreach($injectObject as $inject)
        {
            $dependencies[] = $this->GetObject($inject);

            if(!end($dependencies))
                $this->SetErrorArgs('Unable to inject dependency: '.$inject.' for object '. get_class($injectObject) . ', make sure the class exists', __FILE__, __LINE__)->ThrowError();
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}