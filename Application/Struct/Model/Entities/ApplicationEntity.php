<?php

namespace Application\Entities;


use Application\Core\DatabaseManager;

use Application\Interfaces\Entities\Entity;


abstract class ApplicationEntity extends DatabaseManager implements Entity{

    // Set table name
    protected $tableName;

    // Sets fields for entity
    protected $fields = array();

    public function __construct($params = null) {

        parent::__construct($params);
        $this->BeforeEntityHook();

        $this->setTableForEntity();

        if(is_numeric($params))
            $this->Find($params);
    }

    public function __destruct() {
        $this->AfterEntityHook();
    }

    // Goes with above
    private function setTableForEntity()
    {
        $class = get_called_class();
        $this->tableName = $class::$table;

        return $this;
    }

    /**
     *
     * @param type $property
     * @param type $value
     * @return \Application\Entities\ApplicationEntity
     */
    public function setProperty($property, $value)
    {
        $this->fields[$property] = $value;

        return $this;
    }

    /**
     *
     * @param array $properties
     * @return \Application\Entities\ApplicationEntity
     */
    public function setProperties(array $properties)
    {
        foreach($properties as $property => $value)
        {
            $this->setProperty($property, $value);
        }

        return $this;
    }

    /**
     *
     * @param Mixed $id Can be the primary key value or an array of column and values
     * @return mixed Returns the matching data set from the database.
     */
    public function Find($id = null) {

        return $this->Table($this->tableName, $this->tableColumns)->GetRecordBy($id)->GetResultSet();
    }

    /**
     *
     * @param Mixed $id Can be the primary key value or an array of column and values
     * @return mixed Returns the matching data set from the database.
     */
    public function FindBy(array $params = array()) {

        return $this->CreateEntity($this->Table($this->tableName)->GetOneRecordBy($params));
    }

    /**
     *
     * @param string $entity
     * @return object
     */
    public function GetTableForEntity($entity){

        return $this->Table(str_replace('Entity', '', $entity));
    }

    /**
     *
     * @param array $params Pass in the data for saving it to the database, if not provided<br>
     * the submitted data in globals will be taken and matched to the table on which the operation is applied.
     */
    public function Save($params = array(), array $tables = array()) {

        if(is_object($params))
            $params = $this->ObjectToArray ($params);

        return $this->Table($this->tableName)->QueryOnly($tables)->SaveRecord($params)->GetAffectedRows();
    }

    /**
     *
     * @param int $id the id of the record to be deleted
     * @return int Number of rows affected
     */
    public function Delete($id = null) {

        if (!$id)
            $id = $this->fields[$this->tableName.'.id'];

        return $this->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();
    }

    /**
     *
     * @return type
     */
    public function GetClean()
    {
        return $this->RemoveTableName($this->Table($this->tableName)->GetOneRecordBy(array('id' => $this->id)));
    }

    private function CreateEntity($object)
    {
        return $this->RemoveTableName($object, $this->GetEntity("{$this->tableName}:{$this->tableName}"));
    }

    private function RemoveTableName($object, $newObj = null)
    {
        foreach($object as $key => $obj)
        {
            $key = str_replace($this->tableName.'__', '', $key);
            $newObj->$key = $obj;
        }

        return $newObj;
    }
}