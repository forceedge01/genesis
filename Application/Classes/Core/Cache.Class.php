<?php

namespace Application\Core;



class Cache extends AppMethods{

    public static function CheckForCachedFile($pattern)
    {
        $file = str_replace('//', '/', \Get::Config('Cache.html.folder') . $pattern . '/index.html');

        if(file_exists($file))
        {
            $lastModified = filemtime($file);

            if($lastModified + \Get::Config('Cache.html.expire') < time())
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

        $patt = \Get::Config('Cache.html.folder');

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

    public static function Minify($content, $case)
    {
        switch($case)
        {
            case 'html':
            {
                return preg_replace('/(\s+)/', ' ', preg_replace('/^<!--(.|(\s+))*-->/', '', $content));
            }

            case 'javascript':
            {
                return preg_replace('/(\s+)/', ' ', preg_replace('/\/\*(.(\\s+))*\*\//', '', stream_get_contents($content)));
            }

            case 'css':
            {
                return preg_replace('/(\s+)/', ' ', preg_replace('/\/\*(.(\\s+))*\*\//', '', stream_get_contents($content)));
            }
        }
    }

    public static function Unify($html, array $files)
    {
        $aggregatedContents = null;

        foreach($files as $file)
            $aggregatedContents .= stream_get_contents ($file);

        // Write AggregatedContents to Cache/<filetype>/Unified/aggregated.hash.<filetype>
        // Remove all javascript tags that were included one by one
        // Insert file tag in head section if found, else append
    }

    public static function CompressHtml($content, $case)
    {
        switch($case)
        {
            case 'html':
            {
                return 1;//preg_replace('/(\s+)/', ' ', preg_replace('/^<!--(.|(\s+))*-->/', '', $content));
            }

            case 'javascript':
            {
                return 1;//preg_replace('/(\s+)/', ' ', preg_replace('/\/\*(.(\\s+))*\*\//', '', stream_get_contents($content)));
            }

            case 'css':
            {
                return 1;//preg_replace('/(\s+)/', ' ', preg_replace('/\/\*(.(\\s+))*\*\//', '', stream_get_contents($content)));
            }
        }
    }
}
