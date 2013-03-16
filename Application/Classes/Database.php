<?php

class Database extends Template{

    private
        $host,
        $username,
        $password,
        $name,
        $port,
        $socket;

    public
        $domain,
        $queryResult,
        $queryMeta,
        $verbose,
        $queries,
        $lastQuery,
        $rowsAffected,
        $insert_id,
        $numRows;

    protected
        $activeactiveConnection;

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

        try{

            $this->activeConnection = new mysqli($this->host, $this->username, $this->password, $this->name);

        }
        catch(Exception $e){

            $site = new SiteController();

            if($this->verbose)
                trigger_error ('Error: Removing directory from sites...');
            $site->removeDirectory(SITES_FOLDER . $this->domain);

            trigger_error('Error connecting to mysql Database on INIT: ' . $e->getMessage());
        }
    }

    /**
     *
     * @param string $sql - the SQL query you want to execute
     * @return boolean - true on success, false on failure<br />
     * <br />Execute an SQL query and get appropriate result back in $this->queryResult and $this->rowsAffected
     */
    public function Query($sql){

        try{

            $this->queryResult = null;

            if(!empty($sql)){

                $this->lastQuery = $sql;

                $result = $this->activeConnection->query($sql);

                if(!empty($this->activeConnection->error))
                {

                    if(SHOW_DATABASE_ERRORS)
                        $this->setError('There was a database failure: ' . $this->activeConnection->error . ', SQL query: ' .$sql);

                    if(MAIL_DATABASE_ERROR){

                            $mail = new Mail();

                            $params['to'] = MAIL_DATABASE_ERROR;

                            $params['from_name'] = 'Multisites sql error';

                            $params['from'] = APPLICATION_ADMIN_EMAIL;

                            $params['message'] = 'SQL Query: ' . $sql;

                            $params['message'] .= 'Error: ' . $this->activeConnection->error;

                            $params['subject'] = 'multisite error';

    	            $mail->send($params);
                    }

                    trigger_error($this->activeConnection->error . 'SQL: '.$sql);
                }
                else
                {

                    if(is_object($result))
                        $this->numRows = $result->num_rows;

                    if(@$result->num_rows > 0){

                        if(strpos($sql, 'SELECT ') === 0 || strpos($sql, 'SHOW ') === 0 || strpos($sql, 'show ') === 0 || strpos($sql, 'select ') === 0){

                            while ($row = $result->fetch_object()){

                                $this->queryResult[] = $row;
                            }

                            $result->close();

                        }
                        else{

                            $this->insert_id = $this->activeConnection->insert_id;
                        }

                    }

                    $this->rowsAffected = ($this->activeConnection->affected_rows == -1 ? false : $this->activeConnection->affected_rows);

                    return true;
                }

            }

        }
        catch(Exception $e){

            trigger_error($e->getMessage());
        }

    }

    /**
     *
     * @param string $table - the name of the table you want to insert a record into.
     * @param array $params - array of table column name and its values as $params['keys'] and $params['values']
     * @return boolean - true on success, false on failure<br />
     * <br />Insert a record into a table
     */
    public function Insert($table, $params){

        $params = $this->prepareForInsert($params);

        $sql = 'INSERT INTO '. $table . ' (' . $params['keys'] . ') VALUES (' . $params['values'] . ') ';

        if($this->Query($sql))
            return $this->rowsAffected;
        else
            return false;

    }

    /**
     *
     * @param string $table - name of table to delete from
     * @param array $params - where clause
     * @return boolean - true on success, false on failure<br />
     * <br />Delete a record from a table.
     */
    public function Delete($table, $params){

        $params = $this->prepare($params);

        $sql = 'DELETE FROM '. $table . ' WHERE ' . $params;

        if($this->Query($sql))
            return $this->rowsAffected;
        else
            return false;

    }

    /**
     *
     * @param string $table - table you want to update
     * @param array $params - set clause
     * @return boolean - true on success, false on failure<br />
     * <br />Update a table record(s)
     */
    public function Update($table, $params){

        $params = $this->prepare($params);

        $sql = 'UDPATE '. $table . ' SET ' . $params;

        if($this->Query($sql))
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
    private function prepare($params){

        $query = null;

        foreach($params as $key => $value){

            $query .= $key .' = ';

            if(is_int($value))
                $query .= $value . ' AND ';
            else
                $query .= "'" .$value . "' AND ";
        }

        $query = trim($query, 'AND');

        return $query;
    }

    /**
     *
     * @param array $params - array of params
     * @return type - true on success, false on failure<br />
     * <br />Prepare statement arrays for proper SQL build
     */
    private function prepareForInsert($params){

        $keys = null;
        $values = null;

        foreach($params as $key => $value){

            $keys .= $key .', ';

            if(is_int($value))
                $values .= $value . ',';
            else
                $values .= "'" .$value . "',";
        }

        $params['keys'] = substr($keys, 0, -1);
        $params['values'] = substr($values, 0, -1);

        return $params;
    }

    /**
     * close connection to database
     */
    public function CloseactiveConnection(){
        $this->activeConnection->close();
    }

    /**
     * Begin a transaction
     */
    public function BeginTransaction(){
        $this->activeConnection->autocommit(FALSE);
    }

    /**
     * commit a transaction
     */
    public function Commit(){
        $this->activeConnection->commit();
    }

    /**
     * perform a rollback on a transaction
     */
    public function RollBack(){
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
    public function importSQLFromFile($file){

        try{

            $file = file_get_contents($file);

            $queries = explode('--*--', $file);

            if($this->verbose)
                error_log ('Importing SQL data...<br /><br />');
            foreach($queries as $query){

                $this->Query($query);
            }

            return true;
        }
        catch(Exception $e)
        {

            trigger_error('Error in importSQL: ' . $e->getMessage());
        }
    }

    /**
     *
     * @param string $dbname - database name
     * @return boolean - true on success, false on failure<br />
     * <br />Drop a database, it may not be empty, will drop all tables in it and then drop the database schema.
     */
    public function DropDatabase($dbname = null){

        if(empty($dbname))
            $dbname = $this->domain;

        $tables = $this->Query('Show tables');

        foreach($tables as $table)
        {
            $sql = 'DROP TABLE IF EXISTS `'.$table.'`';

            $this->Query($sql);
        }

        $sql = "drop database $dbname";

        if($this->query($sql))
            return true;
        else
            return false;
    }

    /**
     *
     * @param array Process multiple queries at once.
     *
     */
    public function multiQuery(){

        try{

            if(!empty($this->queries)){

                $this->queryResult = array();

                foreach($this->queries as $sql){

                    $this->queryResult[] = $this->Query($sql);

                }
            }

            unset($this->queries);

            return true;

        }
        catch(Exception $e){

            echo $e->getMessage();

            return false;
        }
    }

    public function databaseExists($dbname){

        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "'.$dbname.'"';

        $this->Query($sql);

        if($this->numRows == 1)
            return true;
        else
            return false;
    }
}