<?php

namespace DatabaseManager\Lib;



use Application\Core\Database;

class BDManager extends Database{

    private static $Query;
    private $tableName;

    /**
     *
     * @param type $table
     * @param type $params
     * @return type
     */
    public function fetchSingle(array $params = array())
    {
        $this->buildQuery($this->tableName, array_merge(array('limit' => 1), $params));
        $this->Query(self::$Query);
        $results = $this->getResultSet();

        return $results[0];
    }

    public function setTable($table)
    {
        $this->tableName = $table;

        return $this;
    }

    /**
     *
     * @param type $entity
     * @return boolean
     */
    public function persist($entity)
    {
        if(! is_object($entity))
            return false;

        $this->setTable($entity::$table);

        if(empty($entity->fields[$entity::$table . '.id']))
            return $this->insert($entity->fields);

        return $this->update($entity->fields, array('id' => $entity->fields[$entity::$table . '.id']));
    }

    /**
     *
     * @param type $table
     * @param type $params
     * @return type
     */
    public function fetchMultiple($table, array $params = array())
    {
        $this->buildQuery($table, $params);
        $this->Query(self::$Query);

        return $this->getResultSet();
    }

    /**
     *
     * @param type $table
     * @param type $params
     * @return type
     */
    public function fetchColumn($table, $params)
    {
        $this->buildQuery($table, array_merge(array('as' => 'just-Column1'), $params));
        $this->Query(self::$Query);
        $result = $this->getResultSet();

        return $result[0]['just-Column1'];
    }

    /**
     *
     * @param type $table
     * @param type $params
     * @param type $where
     * @return type
     */
    public function update(array $params, array $where = array())
    {
        $toUpdate = $this->commaValueString($params);
        $where = $this->whereString($where);

        return $this->Query("UPDATE {$this->tableName} SET $toUpdate WHERE $where");
    }

    public function delete(array $where = array())
    {
        $where = null;

        if($this->isLoopable($where))
            $where = 'WHERE '.$this->commaValueString($where);

        return $this->Query("DELETE FROM {$this->table} $where");
    }

    public function insert($params)
    {
        list($columns, $values) = $this->StringifyParams($params);

        return $this->Query("INSERT INTO {$this->table} ($columns) VALUES ($values)");
    }

    public function setDBQuery($table, $params)
    {
        $this->buildQuery($table, $params);

        return $this;
    }

    /**
     *
     * @param type $table
     * @param type $params
     * @return type
     */
    public function getQuery($table, array $params)
    {
        $this->buildQuery($table, $params);

        return self::$Query;
    }

    public function getLastQuery()
    {
        return self::$Query;
    }

    /**
     *
     * @param array $queries
     * @return array
     */
    public function multiQueryDB(array $queries)
    {
        $results = array();

        foreach($queries as $query)
        {
            $results[] = $this->Query($query);
        }

        return $results;
    }

    private function whereString(array $array)
    {
        $string = null;

        foreach($array as $column => $value)
        {
            $string .= "$column = " . (is_int($value) ? $value : "'$value'" ) . ' AND ';
        }

        return trim($string, ' AND ');
    }

    private function commaValueString(array $array)
    {
        $string = null;

        foreach($array as $column => $value)
        {
            $string .= "$column = " . (is_int($value) ? $value : "'$value'" ) . ', ';
        }

        return trim($string, ', ');
    }

    private function StringifyParams($params)
    {
        $indexed = array();

        foreach($params as $column => $value)
        {
            $indexed[0] .= "$column,";
            $indexed[1] .= ((is_int($value) || strpos($value,'(') != false) ? $value : "'$value'" ) . ',';
        }

        $indexed[0] = trim($indexed[0], ',');
        $indexed[1] = trim($indexed[1], ',');

        return $indexed;
    }

    private function buildQuery($table, array $params = array())
    {
        $columns = '*';

        if(isset($params['columns']))
        {
            $columns = $params['columns'];
        }

        $select = "SELECT {$columns} FROM {$table}";

        if(isset($params['as']))
        {
            $select .= " AS {$params['as']}";
        }

        if(isset($params['left join']))
        {
            $select .= " LEFT JOIN {$params['left join']}";
        }

        if(isset($params['using']))
        {
            $select .= " USING ({$params['using']})";
        }

        if(isset($params['where']))
        {
            $select .= " WHERE {$params['where']}";
        }

        if(isset($params['order by']))
        {
            $select .= " ORDER BY {$params['order by']}";
        }

        if(isset($params['limit']))
        {
            $select .= " LIMIT {$params['limit']}";
        }

        self::$Query = $select;

        return $this;
    }
}