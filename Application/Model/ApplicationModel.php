<?php

namespace Application\Models;



use Application\Interfaces\Models\Model;
use Application\Core\AppMethods;


abstract class ApplicationModel extends AppMethods implements Model{

    protected $entityObject, $observers;

    public function __construct($entityObject = null)
    {
        $this->BeforeModelHook();
        $this->entityObject = $entityObject;
        $this->observers = \Get::Config($this->GetClassFromNameSpacedModel(get_called_class()).'.ModelObservers');
    }

    protected function Attach($observer)
    {
        $this->observers[] = (is_object($observer)) ? get_class($observer) : $observer;
    }

    protected function Detach($observer)
    {
        $unset = (is_object($observer)) ? get_class($observer) : $observer;
        $this->observers = $this->Variable($this->observers)->RemoveValue($unset)->GetVariableResult();

        return $this;
    }

    protected function Nofity($event, $args = null)
    {
        $event .= 'Handler';

        foreach($this->observers as $observer)
        {
            $model = $this->GetModel($observer);
            (method_exists($model, $event)) ? $model->$event($args) : null;
        }

        return $this;
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