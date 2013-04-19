<?php

class facebook extends ApplicationEntity{

      protected
                  $id,
                  $tableColumns,
                  $joinQuery,
                  $tableName;

      public function __construct($id = null){

         parent::__construct();

         $this->tableColumns = array('*');

         $this->tableName = __CLASS__;

         if(is_numeric($id)){

            $this->id = $id;
            $this->Get();

         }
      }

      /**
       *
       * @param Array $param Params can include where clause order by clause or any other mysql clause.
       * @return mixed Returns matching data set.
       */
      public function GetAll(array $params = array()){

        return $this->Table($this->tableName, $this->tableColumns)->GetRecords($params)->GetResultSet();

      }

      /**
       *
       * @param Mixed $id Can be the primary key value or an array of column and values
       * @return mixed Returns the matching data set from the database.
       */
      public function Get($id = null){

        if(!$id)
            $id = $this->id;

        return $this->Table($this->tableName, $this->tableColumns)->GetRecordBy($id)->GetFirstResult();

      }

      /**
       *
       * @param array $params Pass in the data for saving it to the database, if not provided<br>
       * the submitted data in globals will be taken and matched to the table on which the operation is applied.
       */
      public function Save(array $params = array()){

        return $this->Table($this->tableName)->SaveRecord($params)->GetAffectedRows();

      }

      /**
       *
       * @param int $id the id of the record to be deleted
       * @return int Number of rows affected
       */
      public function Delete($id = null){

        if(!$id)
            $id = $this->id;

        return $this->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();

      }
}
              