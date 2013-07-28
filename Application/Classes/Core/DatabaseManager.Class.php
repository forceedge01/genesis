<?php

namespace Application\Core;



class DatabaseManager extends Database{

    private
        $queryTables,
        $queryJoinClause,
        $queryJoins = array(),
        $aggregateTables,
        $queryLimit,
        $queryOrderBy,
        $queryWhere,
        $queryExtra,
        $queryCount,
        $formFields,
        $queryTable,
        $queryColumns,
        $queryTablePrimaryKey,
        $queryTableColumns,
        $foreignKeys = array(),
        $queryParameters = array();

    public function __construct($params = null) {
        parent::__construct($params);
        $this -> aggregateTables = true;
    }

    /**
     *
     * @param type $table
     * @return mixed Gets primary key for a table
     */
    protected function GetPrimaryKey($table = null) {

        if ($table == null)
            $table = $this->queryTable;

        $this->Query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        $this->queryTablePrimaryKey = "`{$table}`.`{$this->queryResult[0]->Column_name}`";

        return $this;
    }

    /**
     *
     * @param type $table
     * @return mixed Gets foreign keys for a table
     */
    protected function ForeignKeys($table = null) {

        $this->foreignKeys = array();

        if ($table == null)
            $table = $this->queryTable;

        $this->Query("SELECT
            table_name as 'TABLE_NAME',
            column_name as 'COLUMN_NAME',
            referenced_table_name as 'REFERENCED_TABLE_NAME',
            referenced_column_name as 'REFERENCED_COLUMN_NAME'
        FROM
            information_schema.key_column_usage
        WHERE
            referenced_table_name is not null
        AND
            TABLE_SCHEMA = '".\Get::Config('Database.name')."'
        AND
            table_name = '$table'");

        if ($this->isLoopable($this->queryResult))
            foreach ($this->queryResult as $key)
            {
                $this->foreignKeys[] = $key;
            }

        $this->multiQuery();

        return $this;
    }

    /**
     *
     * @param type $table
     * @return boolean<br>
     * Sets All the columns for a given table from the database into queryTableColumns
     */
    protected function GetColumns($table = null) {

        if (empty($table))
            $table = $this->queryTable;

        $this->Query("SHOW COLUMNS FROM {$table} FROM " . \Get::Config('Database.name'));

        foreach ($this->queryResult as $columns)
        {
            $columns->Field = $table . '.' . $columns->Field;
            $this->queryTableColumns[] = $columns;
        }

        if ($this->queryTableColumns)
            return $this;
        else
            return false;
    }

    /**
     *
     * @return Object Returns the first result set from a select query
     */
    public function GetFirstResult() {

        if ($this->queriesResult)
            return $this->queriesResult[0];
        else
            return $this->queryResult[0];
    }



    public function GetTableColumns() {

        return $this->queryTableColumns;
    }

    public function GetFormFields() {

        $table = $this->GetTableNameFromNameSpacedEntity(get_called_class());
        $this->Table($table);
        $foreignkeys = $this->ForeignKeys()->GetResultSet();
        $this->formFields[$table] = $this->GetTableColumns();

        if ($foreignkeys)
        {
            foreach ($foreignkeys as $key)
            {
                $this->TableFields($key->REFERENCED_TABLE_NAME);
            }
        }

        return $this->formFields;
    }

    public function GetForeignKeys() {

        return $this->foreignKeys;
    }

    private function TableFields($table) {

        $this->formFields[$table] = $this->Table($table)->GetTableColumns();
        $foreignkeys = $this->ForeignKeys()->GetResultSet();

        if ($foreignkeys)
        {
            foreach ($foreignkeys as $key)
            {
                $this->TableFields($key->REFERENCED_TABLE_NAME);
            }
        }

        return $this;
    }

    public function prepareForTable($table, $params)
    {
        $array = array();

        foreach($params as $key => $value)
            $array[$table.'__'.$key] = $value;

        return $array;
    }

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
     * @param type $id
     * @return Mixed Returns the dataset for a query constructed with the table and Get methods
     */
    protected function queryInit($id = null, array $params = array()) {

        $this
            ->buildRelationsShipsQuery()
            ->createColumnList();

        $extras = null;

        if(is_array($this -> queryExtra))
        {
            foreach($this->queryExtra as $extra)
            {
                $extras .= $extra;
            }
        }

        $query= "SELECT {$extras} {$this->queryColumns} FROM {$this->queryTable} {$this->queryJoinClause}";

        if (is_array($id))
        {
            $params = $this->prepare($id);

            if(!$params)
                $this->setError ('Failed to prepare params, check if field exists in table');

            $query.= ' WHERE ' . $params;

        }
        else if (is_int($id))
        {
            $query.= ' WHERE ' . $this->queryTablePrimaryKey . ' = ' . $id;
        }
        else if($this->queryWhere)
        {
            $query.= ' WHERE ';

            $params = $this->prepare($this->queryWhere);

            $query.= $params;
        }

        if ($this->isLoopable($params))
        {
            $where = $limit = null;

            foreach (@$params as $key => $param)
            {
                if (strtolower($key) == 'where' or strtolower ($key) == 'order by')
                    $where = ' '.strtoupper ($key).' ' . (strpos($param, '.') == false ? $this->queryTable . '.' . $param : $param);

                else if (strtolower ($key) == 'limit')
                    $limit = ' LIMIT ' . $param;
            }

            $query.= $where . $limit;
        }

        if($this->queryOrderBy)
            $query.= ' ORDER BY ' . $this->queryOrderBy;

        if($this->queryLimit)
            $query.= ' LIMIT ' . $this->queryLimit;

        $this -> placeParameters ( )->SetQuery($query);

        return $this;
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

    private function createColumnList(){

        if(empty($this->queryCount))
        {
            $columns = $this->queryColumns;
            $this->queryColumns = null;

            if($this->isLoopable($columns) && $columns[0] != '*')
            {
                foreach ($columns as $column)
                {
                    if(strstr($column, 'as'))
                        $this->queryColumns .= $column . ',';
                    else
                        $this->queryColumns .= "{$column} as '".str_replace('.', '__', $column)."',";
                }
            }

            else if (is_array($this->queryTableColumns))
            {
                $columns = $this->queryTableColumns;
                $this->queryColumns = null;

                foreach ($columns as $column)
                    $this->queryColumns .= "{$column->Field}  as '" . str_replace('.', '__', $column->Field) . "',";
            }

            $this->queryColumns = trim($this->queryColumns, ',');

        }
        else{

            $this->queryColumns = "COUNT(`{$this->queryTable}`.`{$this->queryCount}`)";
        }

        return $this;
    }

    /**
     * Builds relationship data queries using joins
     */
    private function buildRelationsShipsQuery() {

        //!isset ( $this -> queryJoins) and isset($this->foreignKeys) and

        if (count($this->foreignKeys > 0) and $this -> aggregateTables != false)
        {
            $this->queryJoinClause = null;
            $this->RecurseOnTableRelations();
        }

        return $this;
    }

    /**
     * recursive function for relationship query builder
     */
    private function RecurseOnTableRelations() {

        foreach ($this->foreignKeys as $keys)
        {
            if (isset($keys->REFERENCED_TABLE_NAME))
            {
                if($this->isLoopable($this->aggregateTables))
                {
                    foreach($this->aggregateTables as $table)
                    {
                        if($table == $keys->REFERENCED_TABLE_NAME)
                        {
                            $this->GetColumns($keys->REFERENCED_TABLE_NAME);

                            $this->queryJoinClause .= " LEFT JOIN {$keys->REFERENCED_TABLE_NAME} ON {$keys->TABLE_NAME}.{$keys->COLUMN_NAME} = {$keys->REFERENCED_TABLE_NAME}.{$keys->REFERENCED_COLUMN_NAME}";
                            $this
                                 ->ForeignKeys($keys->REFERENCED_TABLE_NAME)
                                 ->RecurseOnTableRelations();

                        }
                    }
                }
                else
                {
                    $this->GetColumns($keys->REFERENCED_TABLE_NAME);

                    $this->queryJoinClause .= " LEFT JOIN {$keys->REFERENCED_TABLE_NAME} ON {$keys->TABLE_NAME}.{$keys->COLUMN_NAME} = {$keys->REFERENCED_TABLE_NAME}.{$keys->REFERENCED_COLUMN_NAME}";

                    $this
                        ->ForeignKeys($keys->REFERENCED_TABLE_NAME)
                        ->RecurseOnTableRelations();
                }
            }
        }
    }

    private function placeParameters ( )
    {
        $this -> query = $this ->Variable ($this -> query) ->Replace ($this -> queryParameters) ->GetVariableResult ();
        return $this;
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
            ->GetPrimaryKey($name)
            ->ForeignKeys()
            ->GetColumns($name);

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

    /**
     *
     * @param array $Tables
     * @return \Application\Core\Database
     * Join specific tables only in a select call
     */
    protected function AggregateOnly(array $Tables){

        $this->aggregateTables = $Tables;
        return $this;
    }

    /**
     *
     * @return \Application\Core\Database
     */
    protected function AggregateNone ( )
    {
        $this -> aggregateTables = false;
        return $this;
    }

    /**
     *
     * @param array $tables
     * @desc Sets the tables on which the queries are to be run with associations
     */
    protected function QueryOnly(array $tables)
    {
        $this -> queryTables = $tables;
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