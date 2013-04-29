<?php

namespace Application\Core\Repositories;



use Application\Core\Database;

class ApplicationRepository extends Database {

    protected
            $id,
            $tableName,
            $tableColumns = array();

    public function __construct($params = null) {

        parent::__construct($params);

        $this->tableName = str_replace('Repository','', $this->GetClassFromNameSpacedClass(get_called_class()));
    }

    /**
     *
     * @param int $id
     * @return object
     * Find one record
     */
    public function find($id){

        $entity = $this->Table($this->tableName)->GetRecordBy($id)->GetResultSet();

        $entityName = $this->tableName . 'Entity';

        ${$this->entity} = new $entityName();

        foreach($entity as $key => $value){

            ${$this->entity}->{$key} = $value;
        }

        return ${$this->entity};
    }

    /**
     *
     * @param array $params
     * @return object
     * Find record by array of params
     */
    public function findOneBy($params){

        $entity = $this->Table($this->tableName)->GetRecordBy($params)->GetResultSet();

        $entityName = $this->entity . 'Entity';

        ${$this->entity} = new $entityName();

        foreach($entity as $key => $value){

            ${$this->entity}->{$key} = $value;
        }

        return ${$this->entity};
    }

    /**
     *
     * @param array $params
     * @return object
     * Find all records, optional parameters for filtering data
     */
    public function findAll($params = array()){

        return $this->Table($this->tableName)->GetRecords($params)->GetResultSet();
    }

    /**
     *
     * @param Array $param Params can include where clause order by clause or any other mysql clause.
     * @return mixed Returns matching data set.
     */
    public function GetAll(array $params = array()) {

        return $this->Table($this->tableName, $this->tableColumns)->GetRecords($params)->GetResultSet();
    }

    /**
     *
     * @param string $column
     * @return int
     * Returns count of table records, if column not set, primary key will be used to count records.
     *
     */
    public function GetCount($column){

        return $this->Table($this->tableName, $this->tableColumns)->Count($column)->GetResultSet();
    }

    /**
     *
     * @param type $repository
     * @return object
     * Gets table for further query processing
     */
    public function GetTableForRepository($repository){

        return $this->Table(str_replace('Repository', '', $repository));
    }

}