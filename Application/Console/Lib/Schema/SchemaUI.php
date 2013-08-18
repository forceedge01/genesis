<?php

namespace Application\Console\Lib;



class SchemaUI extends SchemaApi {

    public function export($db)
    {
        $file = $this->exportDefinition($db);

        if($file)
        {
            echo $this->green ('Export file generation successful: '.$this->blue($file)), $this->linebreak(2);
        }
    }

    public function import($file)
    {
        if($this->importSQL($file))
        {
            echo $this->green ('File '.$file.' imported successfully.'), $this->linebreak(2);
        }
    }

    public function drop($db)
    {
        if($this->DropDB($db))
        {
            echo $this->green("Database '$db' dropped successfully."), $this->linebreak(2);
        }
    }
}