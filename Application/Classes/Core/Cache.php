<?php

namespace Application\Core;



class Cache extends AppMethods{
    
    public static function CheckForCachedFile($pattern)
    {        
        $file = str_replace('//', '/', CACHE_FOLDER . $pattern . '/index.html');
        
        if(file_exists($file))
        {            
            $lastModified = filemtime($file);
            
            if($lastModified + CACHE_EXPIRE < time())
            {
                return false;
            }
            else
            {
                self::OutputFile($file);
            }
        }
        else
            return false;
    }
    
    public static function WriteCacheFile($pattern, $content)
    {   
        $folderChunks = explode('/', $pattern);
        
        $patt = CACHE_FOLDER;
        
        foreach($folderChunks as $f)
        {
            mkdir($patt . '/' . $f);
            $patt .= '/'.$f;
        }
        
        $file = str_replace('//', '/', $patt . '/index.html');
                
        $handle = fopen($file, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }
    
    private static function OutputFile($file)
    {
        ob_start();
        require $file;
        ob_end_flush();
        
        exit;
    }
}
