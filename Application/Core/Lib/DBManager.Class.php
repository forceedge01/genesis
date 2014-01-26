<?php

namespace Application\Core;



use Application\Core\Interfaces\DatabaseManager as DatabaseManagerInterface;

class DatabaseManager extends Database{

	private static $Query;
    
    /**
     * 
     * @param type $table
     * @param type $params
     * @return type
     */
    public function fetchSingle($table, array $params = array())
    {
        $this->buildQuery($table, array_merge(array('limit' => 1), $params));
        $this->Query(self::$Query);
        $results = $this->getResultSet();
        
        return $results[0];
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
    
    protected function setDBQuery($table, $params)
    {
        $this->buildQuery($table, $params);
        
        return $this;
    }

    /**
     * 
     * @param type $method
     * @param type $table
     * @param type $params
     * @return type
     * Run a query on the database without instantiating an object
     */
    public static function StaticDB ($method, $table = null, array $params = array()) 
    {        
        return Base::getObject('Base')->$method($table, $params);
    }
    
    /**
     * 
     * @param type $table
     * @param type $params
     * @param type $where
     * @return type
     */
    public static function updateTable($table, array $params, array $where = array())
    {
        return self::StaticDB('getConnection')->update($table, $params, $where);
    }
    
    public static function queryDB($query)
    {
        self::$Query = $query;
        return self::StaticDB('getConnection')->query($query);
    }
    
    public static function getLastInsertId()
    {
        return self::StaticDB('getConnection')->insert_id;
    }
    
    public static function getNumberOfRowsAffected() 
    {
        return self::StaticDB('getConnection')->rows_affected;
    }
    
    public static function getNumberOfRows()
    {
        return self::StaticDB('getConnection')->num_rows;
    }
    
    /**
     * 
     * @param type $table
     * @param type $params
     * @return type
     */
    public static function getQuery($table, array $params)
    {        
        return self::StaticDB('buildQuery', $table, $params);
    }
    
    public static function deleteFromTable($table, $where = null)
    {
        return self::queryDB("delete from $table where $where");
    }
    
    public static function insertIntoTable($table, $params)
    {
        list($columns, $values) = self::StringifyParams($params);
        
        return self::queryDB("INSERT INTO $table ($columns) VALUES ($values)");
    }
    
    public static function getLastQuery()
    {
        return self::$Query;
    }
    
    private static function StringifyParams($params)
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
    
    /**
     * 
     * @param array $queries
     * @return array
     */
    public static function multiQueryDB(array $queries)
    {
        $results = array();
        
        foreach($queries as $query)
        {
            $results[] = self::queryDB($query);
        }
        
        return $results;
    }
}