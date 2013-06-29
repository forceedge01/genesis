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
                return preg_replace('/(\s+)/', ' ', preg_replace('/<!--.*-->/x', '', $content));
            }

            case 'javascript':
            {
                $content = str_replace(HOST, ROOT, $content);

                $jsContents = preg_replace('/(\s+)/', ' ', preg_replace('!(/\*.*\*/)!xs', '', preg_replace('!\s*//.*(?!\n)!s', "", file_get_contents($content))));

                $directory = dirname($content);
                $fileName = end(explode('/', $content));

                $path = $directory.'/Minified/';
                $path = $directory.'/';
                mkdir($path);

                $handle = fopen($path.$fileName, 'w+');
                fwrite($handle, $jsContents);
                fclose($handle);

                return $path.$file['name'];
            }

            case 'css':
            {
                $content = str_replace(HOST, ROOT, $content);
                $cssContents = preg_replace('/(\s+)/', ' ', preg_replace('!/\*.*?\*/!s', '', file_get_contents($content)));
                $directory = dirname($content);
                $fileName = end(explode('/', $content));

                $path = $directory.'/Minified/';
                $path = $directory . '/';
                mkdir($path);

                $handle = fopen($path.$fileName, 'w+');
                fwrite($handle, $cssContents);
                fclose($handle);

                return $path.$file['name'];
            }
        }
    }

    public static function Unify($html, array $files)
    {
        $aggregatedContents = null;
        $extension = pathinfo($files[0], PATHINFO_EXTENSION);
        $modify = null;
        $tag = null;

        switch($extension)
        {
            case 'js':
            {
                foreach($files as $file)
                {
                    $pattern = "<script .*$file.*></script>";
                    $html = preg_replace('#'.$pattern.'#i', '', $html);
                    $file = str_replace(HOST, ROOT, $file);
                    $modify .= filectime($file);
                    $aggregatedContents .= file_get_contents ($file);
                }

                $modify = hash('sha1', $modify);
                $path = ROOT . 'Public/Assets/Js/Unified/';
                $url = JS_FOLDER . 'Unified/';
                $tag = '<script type="text/javascript" src="'.$url.'unified.'.$modify.'.'.$extension.'"></script>';
                Template::$jsFiles = array($url.'unified.'.$modify.'.'.$extension);

                break;
            }
            case 'css':
            {
                foreach($files as $file)
                {
                    $html = preg_replace('#<link .*'.$file.'.*>#i', '', $html);
                    $file = str_replace(HOST, ROOT, $file);
                    $modify .= filectime(str_replace(HOST, ROOT, $file));
                    $aggregatedContents .= file_get_contents ($file);
                }

                $modify = hash('sha1', $modify);
                $path = ROOT . 'Public/Assets/CSS/Unified/';
                $url = CSS_FOLDER . 'Unified/';
                $tag = '<link rel="stylesheet" href="'.$url.'unified.'.$modify.'.'.$extension.'" type="text/css">';
                Template::$cssFiles = array($url.'unified.'.$modify.'.'.$extension);

                break;
            }
        }

        $fileName = 'unified.'.$modify.'.'.$extension;

        if(!is_file($path.$fileName))
        {
            if(!is_dir($path))
                mkdir($path);

            $files = scandir($path);

            foreach($files as $file)
                if($file != '.' AND $file != '..')
                    unlink($path.'/'.$file);

            $handle = fopen($path.$fileName, 'w+');
            fwrite($handle, $aggregatedContents);
            fclose($handle);
        }

        return $html.$tag;
    }

    public static function Compress($content, $level = 3)
    {
        header('Content-Encoding: gzip');

        if(strlen($content) > 1024)
            return gzencode($content, $level);

        return $content;
    }
}
