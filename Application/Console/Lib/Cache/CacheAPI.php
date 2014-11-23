<?php

namespace Application\Console\Lib;



abstract class CacheAPI extends \Application\Console\Console{

    public function ClearCache()
    {
        $folder = new \Application\Components\Dir();
        if($folder->cleanDirectory(CACHE_FOLDER))
        {
            return $this;
        }

        return false;
    }
}