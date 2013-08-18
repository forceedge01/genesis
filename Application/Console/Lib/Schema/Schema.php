<?php

namespace Application\Console;



class Schema extends Console {

    private $connection, $tables, $database;

    public function __construct() {
        \Application\Core\Loader::LoadCore();
        $this->connection = new \Application\Core\DatabaseManager();
    }

    public function exportDefinition($database = null)
    {
        $this->database = ($database) ? $database : \Get::Config('Database.name');

        $this->tables = $this->connection->GetTables($database);
        $tableDefinition = array();
        $property = 'Tables_in_'.$database;

        if(is_array($this->tables))
        {
            foreach($this->tables as $table)
            {
                $tableDefinition[] = $this->connection->Query('SHOW CREATE TABLE '.$table->$property)->GetResultSet();
            }
        }

        $export = "/**\r\n * Exported by Genesis Simplify Engine\r\n */\r\n\r\n";
        $export .= "-- Export for database: '$database';\r\n\r\n";
        $export .= "CREATE DATABASE IF NOT EXISTS `$database`;\r\nUSE `$database`;\r\n";
        $export .= $this->GetTableDefinitionsAsString($tableDefinition);
        $path = realpath(\Get::Config('Console_Schema.DocsFolder')).'/';

        if(is_dir($path))
        {
            $file = \Get::Config('Console_Schema.Filename').'.sql';

            if($this->createFile($path.$file, $export))
                echo $this->green ('Export file generation successful: '.$this->blue($file) . " in $path"), $this->linebreak(2);
            else
                echo $this->red('Unable to generate export file! Aborting'), $this->linebreak(2);
        }
        else
        {
            echo $this->red('Path provided in schema config does not exist! Path: '.$path), $this->linebreak(2);
        }
    }

    private function CreateFailSafeQuery($sql)
    {
        if(!preg_match('/if not exists/', $sql))
            return preg_replace('/^create(\s)+table[^`\'"]/i' ,'CREATE TABLE IF NOT EXISTS ' , $sql);

        return $sql;
    }

    private function GetTableDefinitionsAsString($tableDefinitions)
    {
        $createTableProperty = 'Create Table';
        $TableDefsString = null;

        foreach($tableDefinitions as $tableDefinition)
        {
            $TableDefsString .= "\r\n-- {$tableDefinition[0]->Table} Table definition\r\n".
                    $this->CreateFailSafeQuery ($tableDefinition[0]->$createTableProperty) . ";\r\n";
            $TableDefsString .= $this->exportData($tableDefinition[0]->Table);
        }

        return $TableDefsString;
    }

    public function exportData($table)
    {

        $data = $this->connection->Query("SELECT * FROM $table")->GetResultSet();

        if($data)
        {
            $dataExport = "\r\n-- Data dump for table $table\r\n";
            $insertsArray = $this->formatForExport($table, $data);

            if(is_array($insertsArray))
                foreach($insertsArray as $insert)
                    $dataExport .= $insert;

            return $dataExport."-- End of data dump for table $table\r\n\r\n--\r\n";
        }

        return null;
    }

    private function formatForExport($table, $data)
    {
        if(is_array($data))
        {
            $ValueRecords = $this->prepareStringFromKeys($data);
            return $this->prepareInsertQueries($table, $ValueRecords);
        }

        return null;
    }

    private function prepareInsertQueries($table, array $records)
    {
        $insertQueries = array();
        $Values = null;

        foreach($records as $key => $valueRecord)
        {
            $Values .= $valueRecord;

            if($key % 2 == 0 and $key !== 0)
            {
                $insertQueries[] = "INSERT INTO $table VALUES ".trim($Values, ', ').";\r\n";
                $Values = null;
            }
        }

        if($Values)
            $insertQueries[] = "INSERT INTO $table VALUES ".trim($Values, ', ').";\r\n";

        return $insertQueries;
    }

    private function prepareStringFromKeys(array $data)
    {
        $FieldsString = null;
        $ValueString = null;
        $separator = ', ';
        $ValueRecords = array();

        foreach($data as $record)
        {
            if(!$FieldsString)
            {
                $FieldsString = '(';
                $ValueString = '(';

                foreach($record as $field => $value)
                {
                    $FieldsString .= $field.$separator;
                    $ValueString .= "'$value'".$separator;
                }

                $FieldsString = trim($FieldsString, $separator).')';
                $ValueRecords[] = trim($ValueString, $separator).')'.$separator;
            }
            else
            {
                $ValueString = ' (';

                foreach($record as $value)
                {
                    $ValueString .= "'$value'".$separator;
                }

                $ValueRecords[] = trim($ValueString, $separator).')'.$separator;;
            }
        }

        return $ValueRecords;
    }

    public function import($file)
    {
        $path = realpath($file);

        if(!is_file($path))
            $this->red('File not found: '.$path);

        // Retrieive clean array of sql queries
        $this->connection->queries = $this->extractQueries(file_get_contents($path));

        try
        {
            $this->connection->multiQuery();
        }
        catch(Exception $e)
        {
            $this->connection->RollBack();
            echo $this->red('Failed import: '.$e->getMessage());
        }
    }

    private function extractQueries($sqlQueries)
    {
        $sqlQueries = preg_replace('/(\s+)/', ' ', preg_replace('!(/\*.*\*/)!xs', '', preg_replace('/--.*/', '', $sqlQueries)));
        $queries = explode(';', $sqlQueries);
        $number = count($queries);
        unset($queries[$number-1]);
        array_walk(&$queries, 'trim');

        return $queries;
    }
}