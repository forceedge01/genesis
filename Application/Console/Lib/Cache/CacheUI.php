<?php

namespace Application\Console\Lib;



class CacheUI extends CacheAPI{

    public function Clear()
    {
        echo $this->linebreak(1), $this->blue('Clearing cache folder at '.\Get::Config('APPDIRS.CACHE_FOLDER'));

        if($this->ClearCache())
        {
            echo $this->AddBreaks($this->green('Cache cleared successfully.'));
        }
        else
        {
            echo $this->AddBreaks($this->red('Unable to clear cache, check permissions and if the directory exists.'));
        }
    }
}