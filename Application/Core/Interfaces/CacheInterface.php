<?php

namespace Application\Core\Interfaces;



/**
 * @author Abdul Wahhab Qureshi
 */
interface CacheInterface{

    /**
     * @param string $pattern the url pattern being requested
     * @param string $content variable containing html code for output
     *
     * @return bool true or false based on creation
     */
    function Create($pattern, $content);

    /**
     * @param string The pattern url requested
     *
     * @return bool True or false whether the file was found and outputted
     */
    function Check($pattern);
}