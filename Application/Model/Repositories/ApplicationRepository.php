<?php

namespace Application\Repositories;



use Application\Core\Database;

use Application\Interfaces\Repositories\Repository;


class ApplicationRepository extends Database implements Repository {

    protected
            $id,
            $tableName,
            $tableColumns = array();

    public function __construct($params = null) {

        parent::__construct($params);
        $this->BeforeRepositoryHook();
        $this->tableName = str_replace('Repository', '', $this->GetClassFromNameSpacedClass(get_called_class()));
    }

    public function __destruct() {
        $this->AfterRepositoryHook();
    }

    /**
     *
     * @param int $id
     * @return object
     * Find one record
     */
    public function find($id, array $tables = array()){

        $entity = $this
                ->Table($this->tableName);

        if(count($tables))
            return $entity
                ->AggregateOnly($tables)
                ->GetRecordBy($id)
                    ->GetResultSet();

        return $entity
                ->GetRecordBy($id)
                    ->GetResultSet();
    }

    /**
     *
     * @param array $params
     * @return object
     * Find record by array of params
     */
    public function findOneBy(array $params){

        return $this
                ->Table($this->tableName)
                ->GetRecordBy($params)
                    ->GetResultSet();
    }

    /**
     *
     * @param array $params
     * @return object
     * Find all records, optional parameters for filtering data
     */
    public function findAll(array $params = array(), $tables = array()){

        $entity = $this
                ->Table($this->tableName, $this->tableColumns);

        if(count($tables))
            return $entity
                ->AggregateOnly($tables)
                ->GetRecords($params)
                    ->GetResultSet() ;

        return $entity
                ->GetRecords($params)
                    ->GetResultSet() ;
    }

    /**
     *
     * @param Array $param Params can include where clause order by clause or any other mysql clause.
     * @return mixed Returns matching data set.
     */
    public function GetAll(array $params = array(), array $tables = array()) {

        $entity = $this
                ->Table($this->tableName, $this->tableColumns);

        if(count($tables))
            return $entity
                ->AggregateOnly($tables)
                ->GetRecords($params)
                    ->GetResultSet() ;

        return $entity
                ->GetRecords($params)
                    ->GetResultSet() ;
    }

    /**
     *
     * @param string $column
     * @return int
     * Returns count of table records, if column not set, primary key will be used to count records.
     *
     */
    public function GetCount($column, $predicament){

        return $this
                ->Table($this->tableName, $this->tableColumns)
                ->Count($column, $predicament)
                ->Execute()
                    ->GetResultSet();
    }

    /**
     *
     * @param type $repository
     * @return object
     * Gets table for further query processing
     */
    private function GetTableForRepository($repository){

        return $this->Table($this->tableName);
    }
}