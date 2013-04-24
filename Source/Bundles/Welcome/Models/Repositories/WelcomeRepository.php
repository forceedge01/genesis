<?php

class WelcomeRepository extends WelcomeEntity{
    
    private
            $activeConnection,
            $selectQueryString,
            $selectJoinQuery,
            $tableName;

    public function __construct($id = null) {

        $this->selectQueryString = "*";
        $this->selectJoinQuery = ""; //Instead of a nested select, using join in queries will dramatically increase your application\'s performance
        $this->tableName = "Welcome";

        if (!empty($id) && is_int($id))
            $this->Get($id);
    }

    public function GetAll() {

        $sql = "select {$this->selectQueryString} from {$this->tableName} {$this->selectJoinQuery}";

        if ($this->activeConnection->Query($sql))
            ;
        return $this->activeConnection->queryResult;
    }

    public function Get($id) {

        $sql = "select {$this->selectQueryString} from {$this->tableName} {$this->selectJoinQuery} where id = $id";

        if ($this->activeConnection->Query($sql))
            ;
        return $this->activeConnection->queryResult;
    }

    public function Create() {

        $sql = "insert into {$this->tableName} () values ()";

        $params = array(

            '' => '',

        );

        if ($this->activeConnection->insert($sql))
            ;
        return $this->activeConnection->queryResult;
    }

    public function Update($id) {

        $sql = "update {$this->tableName} set - = '-' where id = $id";

        if ($this->activeConnection->Query($sql))
            ;
        return $this->activeConnection->queryResult;
    }

    public function Delete($id) {

        $sql = "delete from {$this->tableName} where id = $id";

        if ($this->activeConnection->Query($sql))
            ;
        return $this->activeConnection->queryResult;
    }
}