<?php

namespace Application\Models;



use Application\Interfaces\Models\Model;
use Application\Core\AppMethods;

class ApplicationModel extends AppMethods implements Model {

    protected $entityObject;

    public function __construct($entityObject = null) {

        $this->entityObject = $entityObject;
    }

    public function dump()
    {
        $this->pre($this->entityObject);
        
        return $this;
    }
    
    /**
     * @param string $bundleColonEntity
     * @param array $params
     * @return \Application\Models\ApplicationModel
     * Sets entity object from an array of parameters
     */
    public function SetEntity($bundleColonEntity, $params = array())
    {
        $this->entityObject = $this->GetEntity($bundleColonEntity);
        
        if(count($params) > 0)
        {
            foreach($params as $key => $value)
            {
                $this->entityObject->$key = $value;
            }
        }
        else
        {
            return false;
        }
        
        return $this;
    }
    
    public function GetEntityObject()
    {
        return $this->entityObject;
    }
}