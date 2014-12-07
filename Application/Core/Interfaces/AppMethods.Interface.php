<?php

namespace Application\Core\Interfaces;



interface AppMethods {

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function HashPassword($password);

    /**
     *
     * @param int $length: length of the string to generate.
     * @return a random string generated equals the length specified
     */
    public function GenerateRandomString($length = 10);

    /**
     *
     * @param type $string
     * @return string <br>
     * Returns string with stripped double slashes
     */
    public function StripDoubleSlashes($string);

    /**
     *
     * @param mixed $param
     * @return boolean Checks if a variable is loopable
     */
    public function IsLoopable($param);

    /**
     *
     * @return type
     * Gets the type of the variable
     */
    public function GetType();

    public function Trim(array $list);

    /**
     *
     * @param array $keyedList
     * @return \AppMethods
     * Replace multiple keywords provided in a keyed array
     */
    public function Replace(array $keyedList);

    public function ArrayToString(array $array, $separator = null);

    public function RefactorUrl($url);

    public function ReturnFalse();

    public function ReturnTrue();

    public function LocatePath($key);

    public function GetLocation($file);

    public function ObjectToArray($obj);

    public function ArrayToObject(array $array);
}