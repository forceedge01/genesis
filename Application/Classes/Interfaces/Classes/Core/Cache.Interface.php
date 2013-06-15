<?php

namespace Application\Core\Interfaces;



/**
 * @author Abdul Wahha
 */
interface Cache{

    /**
     * @param string $pattern the url pattern being requested
     * @param string $content variable containing html code for output
     *
     * @return bool true or false based on creation
     */
    public function Create($pattern, $content);

    /**
     * @param string The pattern url requested
     *
     * @return bool True or false whether the file was found and outputted
     */
    public function Check($pattern);
}