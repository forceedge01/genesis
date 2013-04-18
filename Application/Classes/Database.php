<?php

class Database extends Template {

    private
        $host,
        $username,
        $password,
        $name,
        $port,
        $socket,
        $query,
        $queryTable,
        $queryColumns,
        $queryTablePrimaryKey,
        $queryTableColumns,
        $queryJoinClause,
        $foreignKeys,
        $rowsAffected,
        $queryTables,
        $queriesResult,
        $queryResult,
        $activeConnection,
        $lastQuery,
        $numRows,
        $insert_id,
        $formFields,
        $aggregateTables,
        $queryLimit,
        $queryOrderBy,
        $queryWhere,
        $queryExtra;

    protected
        $queryMeta;

    public
        $verbose,
        $queries;

    /**
     *
     * @param array You can specify a port and socket if you want.
     */
    public function __construct($params = null) {

        $this->domain = $params['domain'];

        $this->host = DBHOST;
        $this->username = DBUSERNAME;
        $this->password = DBPASSWORD;
        $this->name = DBNAME;
        $this->port = @$params['port'];
        $this->socket = @$params['socket'];

        try {

            $this->activeConnection = new mysqli($this->host, $this->username, $this->password, $this->name);
        } catch (Exception $e) {

            $site = new SiteController();

            if ($this->verbose)
                trigger_error('Error: Removing directory from sites...');
            $site->removeDirectory(SITES_FOLDER . $this->domain);

            trigger_error('Error connecting to mysql Database on INIT: ' . $e->GetMessage());
        }
    }

    /**
     *
     * @param string $sql - the SQL query you want to execute
     * @return boolean - true on success, false on failure<br />
     * <br />Execute an SQL query and Get appropriate result back in $this->queryResult and $this->rowsAffected
     */
    public function Query($sql = null) {

        try {

            $this->queryResult = null;

            if (empty($sql))
                $sql = $this->query;

            if (!empty($sql)) {

                $this->lastQuery = $sql;

                $result = $this->activeConnection->query($sql);

                if (!empty($this->activeConnection->error)) {

                    if (SHOW_DATABASE_ERRORS)
                        $this->setError('There was a database failure: ' . $this->activeConnection->error . ', SQL query: ' . $sql);

                    if (MAIL_DATABASE_ERROR) {

                        $mail = new Mail();

                        $params['to'] = MAIL_DATABASE_ERROR;

                        $params['from_name'] = 'Multisites sql error';

                        $params['from'] = APPLICATION_ADMIN_EMAIL;

                        $params['message'] = 'SQL Query: ' . $sql;

                        $params['message'] .= 'Error: ' . $this->activeConnection->error;

                        $params['subject'] = 'multisite error';

                        $mail->send($params);
                    }

                    trigger_error($this->activeConnection->error . 'SQL: ' . $sql);
                } else {

                    if (is_object($result))
                        $this->numRows = $result->num_rows;
                    
                    $this->rowsAffected = $this->activeConnection->affected_rows;
                    
                    if (is_numeric($this->activeConnection->insert_id) && $this->activeConnection->insert_id != 0) {

                        $this->insert_id = $this->activeConnection->insert_id;
                    } 
                    else if (@$result->num_rows > 0) {
                        
                        while ($row = $result->fetch_object()) {

                            $this->queryResult[] = $row;
                        }

                        $result->close();
                    }

                    $this->queryMeta = $this->activeConnection->stat;

                    return $this;
                }
            }
        } catch (Exception $e) {

            trigger_error($e->GetMessage());
        }
    }

    /**
     *
     * @param array $params - array of table column name and its values as $params['keys'] and $params['values']
     * @return boolean - true on success, false on failure<br />
     * <br />Insert a record into a table
     */
    private function CreateRecord($params = null) {

        $this->queryTables = array_reverse($this->queryTables);
        
        $queries = array();
        
        if($this->isLoopable($this->queryTables)){
            
            foreach ($this->queryTables as $key => $table) {

                $this->Table($key);

                $params = $this->prepareForInsert($table);
                
                $this->pre($params);

                $queries[] = 'insert into ' . $key . ' (' . $params['keys'] . ') values (' . $params['values'] . ')';
            }
        }
        else{
         
            $params = $this->prepareForInsert($params);
            
            $queries[] = 'insert into ' . $this->queryTable . ' (' . $params['keys'] . ') values (' . $params['values'] . ')';
        }

        $this->queries = $queries;

        if ($this->multiQuery())
            return $this;
        else
            return false;
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
    public function SaveRecord(array $params = array()) {
        if (count($params) == 0)
            $params = $_REQUEST;

        $this->prepareForMultiQuery($params);
        
        $unquotedString = str_replace('`', '', $this->queryTablePrimaryKey);

        if (!isset($params[str_replace('.', '__', $unquotedString)]) && !isset($params[str_replace($this->queryTable . '.', '', $unquotedString)]))
            return $this->CreateRecord($params);
        else
            return $this->UpdateRecord($params);
    }

    /**
     *
     * Updates a record with multiquery
     */
    protected function UpdateRecord($params = null) {

        if($this->isLoopable($this->queryTables)){
            
            foreach ($this->queryTables as $key => $table) {

                $this->Table($key);

                $params = $this->prepare($params, 'update');

                $pkey = $this->GetUnformattedFieldOrKey($this->queryTablePrimaryKey);

                $queries[] = 'UPDATE ' . $this->queryTable . ' SET ' . $params . ' where ' . $this->queryTablePrimaryKey . ' = ' . $table[$pkey];
            }
        }
        else{
            
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

        return str_replace('`', '', end(explode('.', $key)));
    }
    
    protected function GetRawFieldName($key){
        
        return str_replace($this->queryTable . '.', '', (str_replace('`', '', $key)));
    }

    /**
     *
     * @param array $params
     * @return null Work in progress
     * Make a prepare for multiquery and use in save for multiple tables with update and insert queries.
     */
    private function prepareForMultiQuery(array $params = array()) {

        foreach ($params as $key => $value) {

            if (strpos($key, '__') > 0) {

                $tableData = explode('__', $key);

                if (count($tableData) > 0) {

                    $this->queryTables[$tableData[0]][$tableData[1]] = $value;
                }
            }
        }

        return true;
    }

    /**
     *
     * @param type $params - array of params
     * @return type - true on success, false on failure<br />
     * <br />Prepare statement arrays for proper SQl query build.
     */
    private function prepare(array $params = array(), $type = null) {

        $query = null;

        foreach ($params as $key => $value) {

            foreach ($this->queryTableColumns as $column) {

                if ($this->queryTable . '.' . $key == $column->Field && $this->queryTable . '.' . $key != str_replace('`', '', $this->queryTablePrimaryKey)) {

                    $query .= ($this->queryTable ? $this->queryTable . '.' : '' ) . str_replace('__', '.', $key) . ' = ';
                    
                    $mysqlFunctions = array(
                      'CONCAT(',
                      'SUM(',
                      'COUNT('
                    );

                    if (is_numeric($value))
                        $query .= $value . ($type == 'update' ? ',' : ' AND ');
                    else if($this->variable($value)->IsContainedBy($mysqlFunctions))
                        $query .= $value . ($type == 'update' ? ',' : ' AND ');
                    else
                        $query .= "'" . $value . "' " . ($type == 'update' ? ',' : ' AND ');
                }
            }
        }

        $query = trim($query, ' AND ');
        $query = trim($query, ',');

        return $query;
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

        foreach ($params as $key => $value) {

            foreach ($this->queryTableColumns as $column) {

                if ($this->queryTable . '.' . $key == $column->Field) {

                    $keys .= $key . ',';

                    if (is_int($value))
                        $values .= mysql_real_escape_string ($value) . ',';
                    else
                        $values .= "'" . mysql_real_escape_string ($value) . "',";
                }
            }
        }

        $params['keys'] = trim($keys, ',');
        $params['values'] = trim($values, ',');

        return $params;
    }

    /**
     * close connection to database
     */
    public function CloseactiveConnection() {
        $this->activeConnection->close();
    }

    /**
     * Begin a transaction
     */
    public function BeginTransaction() {
        $this->activeConnection->autocommit(FALSE);
    }

    /**
     * commit a transaction
     */
    public function Commit() {
        $this->activeConnection->commit();
    }

    /**
     * perform a rollback on a transaction
     */
    public function RollBack() {
        $this->activeConnection->rollback();
    }

    /**
     *
     * @param string $file
     * The full path to the file, should not be a url
     *
     * @return bool Returns true if success, else will throw an exception.
     *
     * You need to have the SQL file seperated with --*-- symbols for each separate query. <br />
     * The easiest way to do this is a replace function with a certain comment present in the sql file.
     */
    public function importSQLFromFile($file) {

        try {

            $file = file_Get_contents($file);

            $queries = explode('--*--', $file);

            if ($this->verbose)
                error_log('Importing SQL data...<br /><br />');
            foreach ($queries as $query) {

                $this->Query($query);
            }

            return true;
        } catch (Exception $e) {

            trigger_error('Error in importSQL: ' . $e->GetMessage());
        }
    }

    /**
     *
     * @param string $dbname - database name
     * @return boolean - true on success, false on failure<br />
     * <br />Drop a database, it may not be empty, will drop all tables in it and then drop the database schema.
     */
    public function DropDatabase($dbname = null) {

        if (empty($dbname))
            $dbname = $this->domain;

        $tables = $this->Query('Show tables');

        foreach ($tables as $table) {
            $sql = 'DROP TABLE IF EXISTS `' . $table . '`';

            $this->Query($sql);
        }

        $sql = "drop database $dbname";

        if ($this->query($sql))
            return true;
        else
            return false;
    }

    /**
     *
     * @return boolein
     * Process multiple queries at once stored in $queries[]
     */
    public function multiQuery() {

        try {

            if (!empty($this->queries)) {

                $this->queryResult = array();

                foreach ($this->queries as $sql) {

                    $this->Query($sql);

                    if (!empty($this->queryResult))
                        $this->queriesResult[] = $this->queryResult;
                }

                unset($this->queries);
            }

            return true;
        } catch (Exception $e) {

            echo $e->GetMessage();

            return false;
        }
    }

    /**
     *
     * @param string $dbname
     * @return boolean
     * Checks if a database exists
     */
    public function databaseExists($dbname) {

        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "' . $dbname . '"';

        $this->Query($sql);

        if ($this->numRows == 1)
            return true;
        else
            return false;
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

        $this->resetQueryData();

        $this->GetPrimaryKey();

        $this->ForeignKeys();

        $this->GetColumns();

        return $this;
    }

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @return Mixed Returns dataset for a primary key id
     */
    public function GetRecordBy($params) {

        $this->queryInit($params)->Query();

        return $this;
    }

    /**
     *
     * @param mixed $id either an int with the id of the record or an array of filter params
     * @param array $params
     * @return mixed Returns just one of matching records
     */
    public function GetOneRecordBy($id, array $params = array()) {

        $params['limit'] = 1;

        $this->queryInit($id, $params)->Query();

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

        $this->queries = array();
        $this->queriesResult = array();
        $this->foreignKeys = array();
        $this->queryTableColumns = array();
        $this->queryTables = array();
    }

    /**
     *
     * @param type $id
     * @return Mixed Returns the dataset for a query constructed with the table and Get methods
     */
    private function queryInit($id = null, array $params = array()) {

        $this->buildRelationsShipsQuery();
        
        $columns = $this->queryColumns;
        
        $this->queryColumns = null;
        
        if($this->isLoopable($columns)){
                
            foreach ($columns as $column){
                
                if(strstr($column, 'as'))
                    $this->queryColumns .= $column . ',';
                else
                    $this->queryColumns .= $column . ' as "' . str_replace('.', '__', $column) . '",';
                }

            $this->queryColumns = trim($this->queryColumns, ',');
        }

        else if (is_array($this->queryTableColumns)) {

            $columns = $this->queryTableColumns;

            $this->queryColumns = null;

            foreach ($columns as $column)
                $this->queryColumns .= $column->Field . ' as "' . str_replace('.', '__', $column->Field) . '",';

            $this->queryColumns = trim($this->queryColumns, ',');
        }

        $this->query = 'select ' . $this->queryColumns . ' from ' . $this->queryTable . ' ' . $this->queryJoinClause;

        if (is_array($id)) {

            $params = $this->prepare($id);

            $this->query .= ' where ' . $params;

        } else if (is_numeric($id)) {

            $this->query .= ' where ' . $this->queryTablePrimaryKey . ' = ' . $id;
        }

        if($this->queryWhere){
            
            $this->query .= ' where ';
            
            $params = $this->prepare($this->queryWhere);

            $this->query .= $params;
        }

        $limit = null;
        $order = null;

        if ($this->isLoopable($params)) {

            foreach (@$params as $key => $param) {

                if ($key == 'where')
                    $order = ' ' . $key . ' ' . (strpos($param, '.') == false ? $this->queryTable . '.' . $param : $param);

                else if ($key == 'order by')
                    $order = ' ' . $key . ' ' . (strpos($param, '.') == false ? $this->queryTable . '.' . $param : $param);

                else if ($key == 'limit')
                    $limit = ' ' . $key . ' ' . $param;
                    
            }

            $this->query .= $order . $limit;
        }

        if($this->queryOrderBy)
            $this->query .= ' order by ' . $this->queryOrderBy;

        if($this->queryLimit)
            $this->query .= ' limit ' . $this->queryLimit;

        return $this;
    }

    /**
     * Builds relationship data queries using joins
     */
    private function buildRelationsShipsQuery() {

        if (isset($this->foreignKeys) && count($this->foreignKeys != 0)) {

            $this->queryJoinClause = null;

            $this->RecurseOnTableRelations();
        }
    }

    /**
     * recursive function for relationship query builder
     */
    private function RecurseOnTableRelations() {

        foreach ($this->foreignKeys as $keys) {

            if (isset($keys->REFERENCED_TABLE_NAME)) {

                if($this->isLoopable($this->aggregateTables)){

                    foreach($this->aggregateTables as $table){

                        if($table == $keys->REFERENCED_TABLE_NAME){

                            $this->GetColumns($keys->REFERENCED_TABLE_NAME);

                            $this->queryJoinClause .= ' ' .
                            'LEFT JOIN ' . $keys->REFERENCED_TABLE_NAME . ' ' .
                            'ON ' . $keys->TABLE_NAME . '.' . $keys->COLUMN_NAME . ' = ' . $keys->REFERENCED_TABLE_NAME . '.' . $keys->REFERENCED_COLUMN_NAME;

                            $this->ForeignKeys($keys->REFERENCED_TABLE_NAME);

                            $this->RecurseOnTableRelations();

                        }
                    }
                }
                else{

                    $this->GetColumns($keys->REFERENCED_TABLE_NAME);

                    $this->queryJoinClause .= ' ' .
                            'LEFT JOIN ' . $keys->REFERENCED_TABLE_NAME . ' ' .
                            'ON ' . $keys->TABLE_NAME . '.' . $keys->COLUMN_NAME . ' = ' . $keys->REFERENCED_TABLE_NAME . '.' . $keys->REFERENCED_COLUMN_NAME;

                    $this->ForeignKeys($keys->REFERENCED_TABLE_NAME);

                    $this->RecurseOnTableRelations();

                }
            }
        }
    }

    /**
     *
     * @param int $id
     * @return int Returns Rows Affected
     * Delete a record from a table using its primary key
     */
    public function DeleteRecord($id) {

        $this->Query('delete from `' . DBNAME . '`.`' . $this->queryTable . '` where ' . $this->queryTablePrimaryKey . ' = ' . $id);

        return $this;
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

        $this->queryTablePrimaryKey = '`' . $table . '`.`' . $this->queryResult[0]->Column_name . '`';

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

        $this->Query("select
            table_name as 'TABLE_NAME',
            column_name as 'COLUMN_NAME',
            referenced_table_name as 'REFERENCED_TABLE_NAME',
            referenced_column_name as 'REFERENCED_COLUMN_NAME'
        from
            information_schema.key_column_usage
        where
            referenced_table_name is not null
        AND
            table_name = '$table'");

        if ($this->isLoopable($this->queryResult))
            foreach ($this->queryResult as $key) {

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

        if ($table == null)
            $table = $this->queryTable;

        $this->Query("SHOW COLUMNS FROM {$table} FROM " . DBNAME);

        foreach ($this->queryResult as $columns) {

            $columns->Field = $table . '.' . $columns->Field;

            $this->queryTableColumns[] = $columns;
        }

        if ($this->queryTableColumns)
            return true;
        else
            return false;
    }

    /**
     *
     * @return int Number of rows affected from a query
     */
    public function GetAffectedRows() {

        return $this->rowsAffected;
    }

    /**
     *
     * @return int Number of rows in a select query
     */
    public function GetNumberOfRows() {

        return $this->numRows;
    }

    /**
     *
     * @return int the insert id of an insert query
     */
    public function GetInsertID() {

        return $this->insert_id;
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

        $this->Table(get_called_class());

        $foreignkeys = $this->ForeignKeys()->GetResultSet();

        $this->formFields[get_called_class()] = $this->GetTableColumns();
        ;

        if ($foreignkeys)
            foreach ($foreignkeys as $key) {

                $this->TableFields($key->REFERENCED_TABLE_NAME);
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
            foreach ($foreignkeys as $key) {

                $this->TableFields($key->REFERENCED_TABLE_NAME);
            }

        return $this;
    }

    /**
     *
     * @param array $Tables
     * @return \Database
     * Join specific tables only in a select call
     */
    public function AggregateOnly(array $Tables){

        $this->aggregateTables = $Tables;

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
        
        if( $this->variable($tableName)->IsIn( $this->Query('show tables')->GetResultSet() ) )
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
}