<?php

namespace Application\Core;



class DatabaseManager extends Database{

    /**
     *
     * @param array $params - array of table column name and its values as $params['keys'] and $params['values']
     * @return boolean - true on success, false on failure<br />
     * <br />Insert a record into a table
     */
    private function CreateRecord(array $params = array()) {

        $this->queryTables = array_reverse($this->queryTables);
        $queries = array();

        if($this->isLoopable($this->queryTables))
        {
            foreach ($this->queryTables as $key => $table)
            {
                $this->Table($key);
                $params = $this->prepareForInsert($table);
                $queries[] = 'INSERT INTO ' . $key . ' (' . $params['keys'] . ') VALUES (' . $params['values'] . ')';
            }
        }
        else
        {
            $params = $this->prepareForInsert($params);
            $queries[] = 'INSERT INTO ' . $this->queryTable . ' (' . $params['keys'] . ') VALUES (' . $params['values'] . ')';
        }

        $this->queries = $queries;

        if ($this->multiQuery())
            return $this;
        else
            return false;
    }

    /**
     *
     * @param array $params - array of params
     * @return type - true on success, false on failure<br />
     * <br />Prepare statement arrays for proper SQL build
     */
    private function prepareForInsert(array $params = array()) {

        $keys = null;
        $values = null;

        foreach ($params as $key => $value)
        {
            foreach ($this->queryTableColumns as $column)
            {
                if ($this->queryTable . '.' . $key == $column->Field)
                {
                    $keys .= $key . ',';

                    if (is_int($value))
                    {
                        $values .= $this ->filterParam ($value) . ',';
                    }
                    else
                    {
                        $values .= "'" . $this ->filterParam ($value) . "',";
                    }
                }
            }
        }

        $params['keys'] = trim($keys, ',');
        $params['values'] = trim($values, ',');

        return $params;
    }

    /**
     *
     * @param array $params - where clause
     * @return boolean - true on success, false on failure<br />
     * <br />Delete a record from a table.
     */
    public function Delete($params) {

        $params = $this->prepare($params);

        $sql = 'DELETE FROM `' . $this->queryTable . '` WHERE ' . $params;

        if ($this->Query($sql))
            return $this->rowsAffected;
        else
            return false;
    }

    /**
     *
     * @param type $params - array of params
     * @return type - true on success, false on failure<br />
     * <br />Prepare statement arrays for proper SQl query build.
     */
    private function prepare(array $params = array(), $type = null) {

        $query = function ($query = null) use  ($params, $type)
        {
            foreach ($params as $key => $value)
            {
                foreach ($this->queryTableColumns as $column)
                {
                    if ($this->queryTable . '.' . $key == $column->Field)
                    {
                        $query .= ($this->queryTable ? $this->queryTable . '.' : '' ) . str_replace('__', '.', $key) . ' = ';

                        $mysqlFunctions = $this->MySQLFunctions();

                        if (is_int($value) OR $this->variable($value)->has($mysqlFunctions))
                        {
                            $query .= $this ->filterParam ($value )  . ($type == 'update' ? ',' : ' AND ');
                        }
                        else
                        {
                            $query .= "'" . $this ->filterParam ($value )  . "' " . ($type == 'update' ? ',' : ' AND ');
                        }
                    }
                }
            }

            return trim(trim($query, ' AND '),',');

        };

        return $query ( );
    }

    /**
     *
     * @param array $params - set clause
     * @return boolean - true on success, false on failure<br />
     * <br />Update a table record(s)
     */
    protected function SaveRecord($params = array()) {

        if (count($params) == 0)
            $params = $_REQUEST;


        $this->prepareForMultiQuery($params);

        $unquotedString = $this->Variable($this->queryTablePrimaryKey)->Replace( array('`' => '') );

        if (!isset($params[$unquotedString->Replace( array('.' => '__') )]) && !isset($params[$unquotedString->Replace( array('.' => '') )]))
            return $this->CreateRecord($params);

        else
            return $this->UpdateRecord($params);
    }

    /**
     *
     * @param array $params
     * @return null Work in progress
     * Make a prepare for multiquery and use in save for multiple tables with update and insert queries.
     */
    private function prepareForMultiQuery(array $params = array()) {

        foreach ($params as $key => $value)
        {
            $variable = $this->Variable($key);

            if ($variable->Has( array('__') ))
            {
                $tableData = $variable->Explode('__')->GetVariableResult();

                if (count($tableData) > 0)
                {
                    $this->queryTables[$tableData[0]][$tableData[1]] = $value;
                }
            }
        }

        return true;
    }

    /**
     *
     * Updates a record with multiquery
     */
    protected function UpdateRecord($params = null) {

        if($this->isLoopable($this->queryTables))
        {
            foreach ($this->queryTables as $key => $table)
            {
                $this->Table($key);
                $params = $this->prepare($params, 'update');
                $pkey = $this->GetUnformattedFieldOrKey($this->queryTablePrimaryKey);
                $queries[] = 'UPDATE ' . $this->queryTable . ' SET ' . $params . ' WHERE ' . $this->queryTablePrimaryKey . ' = ' . $table[$pkey];
            }
        }
        else
        {
            $pkey = $params[$this->GetRawFieldName($this->queryTablePrimaryKey)];
            $params = $this->prepare($params, 'update');
            $queries[] = 'UPDATE ' . $this->queryTable . ' SET ' . $params . ' where ' . $this->queryTablePrimaryKey . ' = ' . $pkey;
        }

        $this->queries = $queries;

        if ($this->multiQuery())
            return $this;
        else
            return false;
    }

    /**
     *
     * Removed table name and backticks formatting from a field
     */
    protected function GetUnformattedFieldOrKey($key) {

        return $this
                ->Variable($key)
                ->Explode('.')
                ->ReplaceInEach( array('`' => ''))
                    ->GetVariableResult();
    }

    protected function GetRawFieldName($key){

        return $this
                ->Variable($key)
                ->Replace( array('`' => '', '.' => '') )
                    ->GetVariableResult();
    }

    /**
     *
     * @param type $name
     * @param array $columns
     * @param array $conditions
     * return Mixed Sets the current table for operation
     */
    public function Table($name, array $columns = null) {

        $this->queryTable = $name;
        $this->queryColumns = $columns;

        $this
            ->resetQueryData()
            ->GetPrimaryKey()
            ->ForeignKeys()
            ->GetColumns();

        return $this;
    }

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @return Mixed Returns dataset for a primary key id
     */
    public function GetRecordBy($params) {

        $this->queryInit('*', $params)->Query();

        return $this;
    }

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @param array $params
     * @return mixed Returns just one of matching records
     */
    public function GetOneRecordBy(array $params = array()) {

        $params['limit'] = 1;

        $this->queryInit($params)->Query();

        return $this->queryResult[0];
    }

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @param array $params
     * @return boolean Check if a record exists
     */
    public function FindExistanceBy(array $params = array()) {

        $params['limit'] = 1;

        $this->queryInit($params)->Query();

        if ($this->GetNumberOfRows())
            return true;
        else
            return false;
    }

    /**
     *
     * @param array $params
     * @return mixed Get All records either filtered or not.
     */
    public function GetRecords(array $params = array()) {

        $this->queryInit('*', $params)->Query();

        return $this;
    }

    /**
     *
     * @return object Gets the result set from the database object for a processed query
     */
    public function GetResultSet() {

        if ($this->queriesResult)
            return $this->queriesResult;
        else
            return $this->queryResult;
    }

    private function resetQueryData() {

        $this->queries = $this->queriesResult = $this->foreignKeys = $this->queryTableColumns = $this->queryTables = array();

        return $this;
    }

    /**
     *
     * @param int $id
     * @return int Returns Rows Affected
     * Delete a record from a table using its primary key
     */
    public function DeleteRecord($id) {

        $this->Query("DELETE FROM `".\Get::Config('Database.name')."`.`{$this->queryTable}` WHERE {$this->queryTablePrimaryKey} = {$id}");

        return $this;
    }

    public function Select(array $list){

        $this->queryColumns = $list;
        return $this;
    }

    public function Where(array $list){

        $this->queryWhere = $list;
        return $this;
    }

    public function GroupBy(array $list){

        $this->queryGroupBy = $list;
        return $this;
    }

    public function OrderBy($column){

        $this->queryOrderBy = $column;
        return $this;
    }

    public function Limit($int){

        $this->queryLimit = $int;
        return $this;
    }

    public function Extra(array $list){

        $this->queryExtra = $list;
        return $this;
    }

    public function Execute(){

        $this->queryInit('*')->Query();
        return $this;
    }

    public function TableExists($tableName){

        if( $this->Variable($tableName)->IsIn( $this->Query('SHOW TABLES')->GetResultSet() ) )
            return true;
        else
            return false;
    }

    public function RecordExists(array $params){

        if($this->queryInit($params)->Query()->GetNumberOfRows() > 0)
            return true;
        else
            return false;
    }

    public function Count($column = null, $predicament = null){

        if(empty($column))
            $column = $this->queryTablePrimaryKey;

        $this -> queryWhere = $predicament;
        $this->queryCount = $column;

        return $this;
    }

    public function SetParameter ($key, $value){

        $this -> queryParameters[':'.$key] = $this ->filterParam( $value ) ;
        return $this;
    }

    public function SetParameters (array $keyedValues){

        foreach ( $keyedValues as $key => $value )
            $this ->SetParameter ($key, $value);

        return $this;
    }

    protected function filterParam ( $value )
    {
        return mysql_real_escape_string ( $value ) ;
    }

    public function LeftJoin ( $table, $alias, $predicament )
    {
        $this -> queryJoins[] = $table .' AS '.$alias.' ON '.$predicament;
        return $this;
    }
}