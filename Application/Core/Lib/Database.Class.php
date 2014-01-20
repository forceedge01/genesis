<?php

namespace Application\Core;


use Application\Core\Interfaces\Database as DatabaseInterface;

abstract class Database extends Template implements DatabaseInterface{

    private
        $threadId,
        $host,
        $username,
        $password,
        $name,
        $port,
        $socket,
        $query,
        $rowsAffected,
        $activeConnection,
        $lastQuery,
        $numRows,
        $insert_id;

    protected
        $queryMeta,
        $queriesResult,
        $queryResult;

    public
        $verbose,
        $queries;

    /**
     *
     * @param array You can specify a port and socket if you want.
     */
    public function __construct($params = null) {

        if(!\Get::Config('Database.connect'))
        {
            $this
                ->SetErrorArgs('Attempting to connect to database while connection turned off in config.', 'Database.Config.php', '0', '1')
                ->ThrowException();
        }

        $this->domain = $params['domain'];

        $this->host = \Get::Config('Database.host');
        $this->username = \Get::Config('Database.username');
        $this->password = \Get::Config('Database.password');
        $this->name = \Get::Config('Database.name');
        $this->port = @$params['port'];
        $this->socket = @$params['socket'];

        try {

            $driver = \Get::Config('Database.driver');

            switch($driver)
            {
                case 'mysqli':
                    $this->activeConnection = new \PDO("mysql:host={$this->host};dbname={$this->name}", $this->username, $this->password);
                    break;
                case 'sql':
                    break;
                case 'oracle':
                    break;
                case 'sqlite':
                    break;
                case 'postgreSQL':
                    break;
                default:
//                    $this->activeConnection = new \mysqli($this->host, $this->username, $this->password, $this->name);
                    break;
            }

            if ($this->activeConnection->errorCode())
            {
                $this
                    ->SetErrorArgs(
                            'Connect Error ' . $this->activeConnection->errorInfo(),
                            'Database.Config.php',
                            '0',
                            $this->activeConnection->errorCode()
                        )
                    ->ThrowError();
            }

            $this->activeConnection->set_charset('utf8');

        } catch (Exception $e) {

            trigger_error('Error connecting to MySQL Database on INIT: ' . $e->GetMessage());
        }
    }

    public function __destruct() {
        $this->activeConnection = null;
    }

    public function GetConnection ( )
    {
        return $this -> activeConnection;
    }

    protected function SetQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     *
     * @param string $sql - the SQL query you want to execute
     * @return boolean - true on success, false on failure<br />
     * <br />Execute an SQL query and Get appropriate result back in $this->queryResult and $this->rowsAffected
     */
    public function Query($sql = null)
    {
        try
        {
            $this->queryResult = null;

            if (empty($sql))
                $sql = $this->query;

            if (!empty($sql))
            {
                $this->lastQuery = $sql;
                $statement = $this->activeConnection->query($sql);

                $result = $statement->execute();

                if (!empty($statement->errorInfo()))
                {
                    if (\Get::Config('Errors.showDBErrors'))
                    {
                        if(isset($_SERVER['SERVER_NAME']))
                            $this->SetErrorArgs('There was a database failure: ' . $statement->errorInfo() . ', SQL query: ' . $sql ,'Ref backtrace','0','2')->ThrowError();
                    }

                    if (\Get::Config('Errors.mailDBErrors'))
                    {
                        $mail = new \Application\Components\Mailer();

                        $params['to'] = \Get::Config('Application.Admin_Email');
                        $params['from_name'] = 'Multisites sql error';
                        $params['from'] = \Get::Config('Errors.errorsEmailAddress');
                        $params['message'] = 'SQL Query: ' . $sql;
                        $params['message'] .= 'Error: ' . $statement->errorInfo();
                        $params['subject'] = 'multisite error';

                        $mail->send($params);
                    }

                    trigger_error($statement->errorInfo() . 'Error in SQL: ' . $sql);
                }
                else
                {
                    if ($result)
                    {
                        $this->numRows = $statement->rowCount();

                        if (is_numeric($this->activeConnection->lastInsertId()) && $this->activeConnection->lastInsertId() != 0)
                        {
                            $this->insert_id = $this->activeConnection->lastInsertId();
                        }

                        if ($this->numRows > 0)
                        {
                            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
                            {
                                $this->queryResult[] = $row;
                            }
                        }

                        $statement->closeCursor();
                    }

                    $this->rowsAffected = $statement->rowCount();

                    return $this;
                }
            }
            else
                $this->SetErrorArgs('Empty query given to execute', 'Database', '0', '3')->ThrowError();

        } catch (Exception $e) {

            trigger_error($e->GetMessage());
        }
    }

    protected function MySQLFunctions()
    {
        return array(

            'CONCAT(',
            'SUM(',
            'COUNT(',
            'DISTINCT(',
            'MAX(',
            'DATE',
            'DATE_FORMAT(',
            'CURRENT_DATE(',
            'CURRENT_TIME(',
            'TIMESTAMP('
        );
    }

    /**
     * close connection to database
     */
    public function CloseActiveConnection() {
        $this->activeConnection = null;
    }

    /**
     * Begin a transaction
     */
    public function BeginTransaction() {
        $this->activeConnection->beginTransaction();
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
        $this->activeConnection->rollBack();
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

        try
        {
            $file = file_Get_contents($file);

            $queries = explode('--*--', $file);

            if ($this->verbose)
            {
                error_log('Importing SQL data...<br /><br />');
            }

            foreach ($queries as $query)
            {

                $this->Query($query);
            }

            return true;
        }
        catch (Exception $e)
        {
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

        $tables = $this->Query('SHOW TABLES');

        foreach ($tables as $table)
        {
            $this->queries[] = "DROP TABLE IF EXISTS `$table`";
        }

        $this->queries[] = "DROP DATABASE `$dbname`";

        if ($this->multiQuery())
            return $this;
        else
            return false;
    }

    /**
     *
     * @return boolein
     * Process multiple queries at once stored in $queries[],
     * Is transactions safe
     */
    public function multiQuery() {

        $this->BeginTransaction();

        try
        {
            if (!empty($this->queries))
            {
                $this->queryResult = array();

                foreach ($this->queries as $sql)
                {
                    $this->Query($sql);

                    if (!empty($this->queryResult))
                        $this->queriesResult[] = $this->queryResult;
                }

                $this->Commit();
                unset($this->queries);
            }

            return $this;
        }
        catch (Exception $e)
        {
            $this->RollBack();
            $this->SetErrorArgs($e->GetMessage(),'Database:MultiQuery','0','4')->ThrowException();
        }
    }

    /**
     *
     * @param string $dbname
     * @return boolean
     * Checks if a database exists
     */
    public function DatabaseExists($dbname)
    {
        $this->Query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");

        if ($this->numRows == 1)
            return true;
        else
            return false;
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
}