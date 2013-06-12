<?php

namespace Application\Console;



class Watcher extends Console{
    
    private 
            $dir,
            $service;
    
    public function __construct($directory, $service) {
        
        $this -> dir = $directory;
        $this -> service = $service;
    }
    
    public function automate()
    {
        $hash1 = $this ->MD5_dir($this -> dir);
        
        echo $this ->linebreak(2) , 'Watching Directory ' . $this -> dir;
        
        while(true)
        {            
            $testHash = $this ->MD5_dir($this -> dir);
            
            if($hash1 != $testHash)
            {   
                $this ->switchOption($this -> service);
                $hash1 = $testHash;
            }
            
            sleep(3);
        }
    }
    
    private function MD5_dir($directory)
    {
        if(!is_dir($directory))
            return false;
        
        $md5Files = array();
        $files = array_diff(scandir($directory), array('.', '..'));
        
        foreach($files as $file)
        {
            $filePath = str_replace('//','/', $directory . '/' . $file) ;
            
            if(is_dir($filePath))
            {
                $md5Files[] = $this -> MD5_dir ($filePath);
            }
            else
            {
                $md5Files[] = md5_file($filePath);
            }
        }
        
        return md5(implode('', $md5Files));        
    }
}
