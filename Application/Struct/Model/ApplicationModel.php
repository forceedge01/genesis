<?php

namespace Application\Models;



use Application\Interfaces\Models\Model;
use Application\Core\EventHandler;


abstract class ApplicationModel extends EventHandler implements Model{

    private $entityObject, $observers;

    public function __construct($entityObject = null)
    {
        $this->BeforeModelHook();
        $this->entityObject = $entityObject;
        $this->observers = \Get::Config($this->GetClassFromNameSpacedModel(get_called_class()).'.ModelObservers');
    }

    protected function __destruct()
    {
        $this->AfterModelHook();
    }

    protected function dump()
    {
        $this->pre($this->entityObject);

        return $this;
    }

    /**
     * @param string $bundleColonEntity
     * @param array $params
     * @return \Application\Models\ApplicationModel
     * Sets entity object from an array of parameters if provided for the model to use, if not will set an empty entity to use.
     */
    protected function SetEntity($bundleColonEntity, $params = array())
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

    protected function GetEntityObject()
    {
        return $this->entityObject;
    }
}