<?php

namespace Application\Models;



use Application\Interfaces\Models\Model;
use Application\Core\AppMethods;

class ApplicationModel extends AppMethods implements Model {

    protected
            $entityObject;


    public function __construct($entityObject) {

        $this->entityObject = $entityObject;
    }

    public function dump()
    {
        $this->pre($this->entityObject);
    }
}