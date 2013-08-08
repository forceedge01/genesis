<?php

namespace Application\Core;



class Cache extends AppMethods{

    private function __construct() {}
    private function __clone() {}

    public static function CheckForCachedFile($pattern)
    {
        $file = str_replace('//', '/', \Get::Config('CORE.CACHE_FOLDER') . $pattern . '/index.html');

        if(file_exists($file))
        {
            if(filemtime($file) + \Get::Config('Cache.html.expire') < time())
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

        $patt = \Get::Config('CORE.CACHE_FOLDER');

        foreach($folderChunks as $f)
        {
            $dir = $patt . '/' . $f;

            if(!is_dir($dir))
                mkdir($dir);

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
        die();
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
            case 'css':
            {
                $content = str_replace(HOST, ROOT, $content);

                if($case == 'javascript')
                    $Contents = preg_replace('/(\s+)/', ' ', preg_replace('!(/\*.*\*/)!xs', '', preg_replace('!\s*//.*(?!\n)!s', "", file_get_contents($content))));
                else
                    $Contents = preg_replace('/(\s+)/', ' ', preg_replace('!/\*.*?\*/!s', '', file_get_contents($content)));

                $directory = dirname($content);
                $fileName = end(explode('/', $content));

                $path = $directory.'/Minified/';
                $path = $directory.'/';

                if(!is_dir($path))
                    mkdir($path);

                $handle = fopen($path.$fileName, 'w+');
                fwrite($handle, $Contents);
                fclose($handle);

                return $path.$fileName;
            }
        }
    }

    public static function Unify($html, array $files)
    {
        $aggregatedContents = null;
        $extension = pathinfo($files[0], PATHINFO_EXTENSION);
        $modify = null;
        $tag = null;
        $placement = null;

        switch($extension)
        {
            case 'js':
            {
                foreach($files as $file)
                {
                    $html = preg_replace("#<script .*$file.*></script>#i", '', $html);
                    $file = str_replace(HOST, ROOT, $file);
                    $modify .= filectime($file);
                    $aggregatedContents .= file_get_contents ($file);
                }

                $modify = hash('sha1', $modify);
                $path = ROOT . 'Public/Assets/Js/Unified/';
                $url =  \Get::Config('CORE.TEMPLATING.JS_FOLDER') . 'Unified/';
                $tag = '<script type="text/javascript" src="'.$url.'unified.'.$modify.'.'.$extension.'"></script>';
                Template::$jsFiles = array($url.'unified.'.$modify.'.'.$extension);

                $placement = explode('-', \Get::Config('Cache.javascript.placement'));

                break;
            }
            case 'css':
            {
                foreach($files as $file)
                {
                    $html = preg_replace('#<link .*'.$file.'(?!>).*/>#i', '', $html);
                    $file = str_replace(HOST, ROOT, $file);
                    $modify .= filectime($file);
                    $aggregatedContents .= file_get_contents ($file);
                }

                $modify = hash('sha1', $modify);
                $path = ROOT . 'Public/Assets/CSS/Unified/';
                $url = \Get::Config('CORE.TEMPLATING.CSS_FOLDER') . 'Unified/';
                $tag = '<link rel="stylesheet" href="'.$url.'unified.'.$modify.'.'.$extension.'" type="text/css">';
                Template::$cssFiles = array($url.'unified.'.$modify.'.'.$extension);

                $placement = explode('-', \Get::Config('Cache.css.placement'));

                break;
            }
        }

        if($placement[0] == 'endof')
        {
            $html = str_replace('</'.$placement[1], $tag.'</'.$placement[1], $html);
        }
        else if($placement[1])
        {
            $html = str_replace('<'.$placement[1].'>', "<{$placement[1]}>$tag", $html);
        }
        else
        {
            $html = str_replace('<head>', "<head>$tag", $html);
        }

        $fileName = 'unified.'.$modify.'.'.$extension;

        if(!is_file($path.$fileName))
        {
            if(!is_dir($path))
                mkdir($path);

            $files = scandir($path);

            foreach($files as $file)
                if($file != '.' AND $file != '..')
                    unlink($path.$file);

            $handle = fopen($path.$fileName, 'w+');
            fwrite($handle, $aggregatedContents);
            fclose($handle);
        }

        return $html;
    }

    public static function Compress($content, $level = 3)
    {
        header('Content-Encoding: gzip');

        if(strlen($content) > 1024)
            return gzencode($content, $level);

        return $content;
    }
}
