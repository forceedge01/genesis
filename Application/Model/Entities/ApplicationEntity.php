<?php

namespace Application\Entities;


use Application\Core\Database;

use Application\Interfaces\Entities\Entity;


class ApplicationEntity extends Database implements Entity{

    protected
            $id,
            $tableName;

    public function __construct($params = null) {

        parent::__construct($params);

        $this->tableName = str_replace('Entity','', $this->GetClassFromNameSpacedClass(get_called_class()));

        if(is_numeric($params))
            $this->Get($params);
    }

    /**
     *
     * @param Mixed $id Can be the primary key value or an array of column and values
     * @return mixed Returns the matching data set from the database.
     */
    public function Get($id = null) {

        if (!$id)
            $id = $this->id;

        return $this->Table($this->tableName, $this->tableColumns)->GetRecordBy($id)->GetResultSet();
    }

    /**
     *
     * @param Mixed $id Can be the primary key value or an array of column and values
     * @return mixed Returns the matching data set from the database.
     */
    public function Find(array $params = array()) {

        return $this->RemoveTableNameFromFields($this->Table($this->tableName)->GetOneRecordBy($params));
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
    public function Save(array $params = array(), array $tables = array()) {

        return $this->Table($this->tableName)->QueryOnly($tables)->SaveRecord($params)->GetAffectedRows();
    }

    /**
     *
     * @param int $id the id of the record to be deleted
     * @return int Number of rows affected
     */
    public function Delete($id = null) {

        if (!$id)
            $id = $this->id;

        return $this->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();
    }

    protected function RemoveTableNameFromFields($object)
    {
        $newObj = null;
        foreach($object as $key => $obj)
        {
            $key = str_replace($this->tableName.'__', '', $key);
            $newObj->$key = $obj;
        }

        return $newObj;
    }
}