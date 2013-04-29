<?php

namespace Application\Core\Entities;


use Application\Core\Database;

class ApplicationEntity extends Database{

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
    public function Save(array $params = array()) {

        return $this->Table($this->tableName)->SaveRecord($params)->GetAffectedRows();
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
}