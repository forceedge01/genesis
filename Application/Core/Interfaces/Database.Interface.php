<?php

namespace Application\Core\Interfaces;

interface Database{

    /**
     *
     * @param array You can specify a port and socket if you want.
     */
    public function __construct($params = null) ;

    public function __destruct() ;

    public function GetConnection ();

    /**
     *
     * @param string $sql - the SQL query you want to execute
     * @return boolean - true on success, false on failure<br />
     * <br />Execute an SQL query and Get appropriate result back in $this->queryResult and $this->rowsAffected
     */
    public function Query($sql = null) ;

    /**
     * close connection to database
     */
    public function CloseActiveConnection() ;

    /**
     * Begin a transaction
     */
    public function BeginTransaction() ;

    /**
     * commit a transaction
     */
    public function Commit() ;

    /**
     * perform a rollback on a transaction
     */
    public function RollBack() ;

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
    public function importSQLFromFile($file) ;

    /**
     *
     * @param string $dbname - database name
     * @return boolean - true on success, false on failure<br />
     * <br />Drop a database, it may not be empty, will drop all tables in it and then drop the database schema.
     */
    public function DropDatabase($dbname = null) ;

    /**
     *
     * @param string $dbname
     * @return boolean
     * Checks if a database exists
     */
    public function DatabaseExists($dbname);

    /**
     *
     * @return object Gets the result set from the database object for a processed query
     */
    public function GetResultSet() ;

    /**
     *
     * @return int Number of rows affected from a query
     */
    public function GetAffectedRows() ;

    /**
     *
     * @return int Number of rows in a select query
     */
    public function GetNumberOfRows() ;

    /**
     *
     * @return int the insert id of an insert query
     */
    public function GetInsertID() ;
}