<?php

namespace Application\Entities;


use Application\Core\DatabaseManager;

use Application\Interfaces\Entities\Entity;


abstract class ApplicationEntity extends DatabaseManager implements Entity{

    protected $tableName;
    private $table;
    public $id;

    public function __construct($params = null) {

        parent::__construct($params);
        $this->BeforeEntityHook();

        $this->getTable();

        if(is_numeric($params))
            $this->Get($params);

        
    }
    
    public function setTable($table)
    {
        $this->tableName = $table;
        
        return $this;
    }
    
    private function getTable()
    {
        $class = get_called_class();
        $this->tableName = $class::$table;
        
        return $this;
    }

    public function __destruct() {
        $this->AfterEntityHook();
    }

    /**
     *
     * @param Mixed $id Can be the primary key value or an array of column and values
     * @return mixed Returns the matching data set from the database.
     */
    public function Find($id = null) {

        if (!$id)
            $id = $this->id;

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

    public function SetTableName($table)
    {
        $this->tableName = $table;
        return $this;
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
            $id = $this->id;

        return $this->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();
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

    public function GetId()
    {
        return $this->id;
    }

    public function GetClean()
    {
        return $this->RemoveTableName($this->Table($this->tableName)->GetOneRecordBy(array('id' => $this->id)));
    }

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
    public function update(array $params, array $where = array())
    {
        $toUpdate = columnToValue($params);
        $where = columnToValue($where);
        
        return $this->Query("UPDATE {$this->tableName} SET $toUpdate WHERE $where");
    }

    private function columnToValue(array $array)
    {
        $string = '';

        foreach($array as $column => $value)
        {
            $string .= "$column = " . (is_int($value) ? $value : "'$value'" ) . ' and ';
        }

        return trim($string, ' and ');
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
    
    public function delete($where = null)
    {
        return $this->Query("delete from {$this->table} where $where");
    }
    
    public function insert($params)
    {
        list($columns, $values) = self::StringifyParams($params);
        
        return $this->Query("INSERT INTO {$this->table} ($columns) VALUES ($values)");
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