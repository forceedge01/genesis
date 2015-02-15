<?php

namespace Application\Core\Interfaces;



interface DatabaseManager {

    public function __construct($params = null);

    /**
     *
     * @return Object Returns the first result set from a select query
     */
    public function GetFirstResult() ;

    public function GetTableColumns() ;

    public function GetFormFields() ;

    public function GetForeignKeys() ;

    /**
     *
     * @param type $table
     * @param type $params
     * @return type
     */
    public function prepareForTable($table, $params);

    /**
     *
     * @param array $params - where clause
     * @return boolean - true on success, false on failure<br />
     * <br />Delete a record from a table.
     */
    public function Delete($params) ;

    /**
     *
     * @param type $name
     * @param array $columns
     * @param array $conditions
     * return Mixed Sets the current table for operation
     */
    public function Table($name, array $columns = null) ;

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @return Mixed Returns dataset for a primary key id
     */
    public function GetRecordBy($params) ;

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @param array $params
     * @return mixed Returns just one of matching records
     */
    public function GetOneRecordBy(array $params = array()) ;

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @param array $params
     * @return boolean Check if a record exists
     */
    public function FindExistanceBy(array $params = array());

    /**
     *
     * @param array $params
     * @return mixed Get All records either filtered or not.
     */
    public function GetRecords(array $params = array()) ;

    /**
     *
     * @param int $id
     * @return int Returns Rows Affected
     * Delete a record from a table using its primary key
     */
    public function DeleteRecord($id) ;

    public function Select(array $list);

    public function Where(array $list);

    public function GroupBy(array $list);

    public function OrderBy($column);

    public function Limit($int);

    public function Extra(array $list);

    public function Execute();

    public function TableExists($tableName);

    public function RecordExists(array $params);

    public function Count($column = null, $predicament = null);

    public function SetParameter ($key, $value);

    public function SetParameters (array $keyedValues);

    public function LeftJoin ( $table, $alias, $predicament );

    public function GetTables($database = null);
}