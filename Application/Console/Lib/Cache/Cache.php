<?php

namespace Application\Console;



class Cache extends Console{

    public function Clear()
    {
        echo $this->linebreak(1) . $this->blue('Clearing cache folder at '.\Get::Config('CORE.CACHE_FOLDER'));

        $folder = new \Application\Components\Dir();
        if($folder->cleanDirectory(CACHE_FOLDER))
        {
            echo $this->linebreak(1) . $this->green('Cache cleared successfully.');
            echo $this->linebreak(1);
        }
        else
        {
            echo $this->linebreak(1) . $this->red('Unable to clear cache, check permissions and if the directory exists.');
            echo $this->linebreak(1);
        }
    }
}