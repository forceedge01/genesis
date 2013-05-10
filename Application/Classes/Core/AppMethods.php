<?php

namespace Application\Core;



class AppMethods extends Debugger{

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function HashPassword($password) {

        return hash(AUTH_PASSWORD_ENCRYPTION_ALGORITHM, $password);
    }

    /**
     *
     * @param int $length: length of the string to generate.
     * @return a random string generated equals the length specified
     */
    public function GenerateRandomString($length = 10) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     *
     * @param type $string
     * @return string <br>
     * Returns string with stripped double slashes
     */
    public function StripDoubleSlashes($string) {

        return str_replace('//', '/', $string);
    }

    /**
     *
     * @param mixed $param
     * @return boolean Checks if a variable is loopable
     */
    public function IsLoopable($param) {

        if (isset($param) && ((is_array($param) || is_object($param)) && count($param) != 0 ))
            return true;
        else
            return false;
    }

    /**
     *
     * @param type $subject
     * @param type $haystack
     * @return type
     * Returns result for human readable string of conditional statement in an array or object
     */
    /*public function find($subject, $haystack) {

        $result = array();

        $subject = strtolower($subject);

        if (strpos($subject, 'or'))
            $ors = explode('or', $subject);

        if ($this->isLoopable($haystack)) {

            foreach ($haystack as $stack) {

                if ($this->isLoopable($ors)) {
                    foreach ($ors as $or) {

                        if (strpos($or, 'and'))
                            $ands = explode('and', $or);

                        if ($this->isLoopable($ands)) {

                            $and = true;
                            $position = array();

                            foreach ($ands as $an) {

                                if ($and)
                                    if (!strpos($stack, $an)) {

                                        $and = false;
                                        $position[$an][$stack] = strpos($stack, $an);
                                    }
                            }

                            if ($and) {

                                $result[$or][$stack] = $position;
                            }
                        }

                        $result[$or][$stack] = strpos($stack, $or);
                    }
                } else {

                    $ands = explode('and', $subject);

                    if ($this->isLoopable($ands)) {

                        $and = true;
                        $position = array();

                        foreach ($ands as $an) {

                            if ($and)
                                if (!strpos($stack, $an)) {

                                    $and = false;
                                    $position[$an][$stack] = strpos($stack, $an);
                                }
                        }

                        if ($and) {

                            $result[$or][$stack] = $position;
                        }
                    }

                    $result[$or][$stack] = strpos($stack, $or);
                }
            }
        } else {

            $stack = $haystack;

            if ($this->isLoopable($ors)) {

                foreach ($ors as $or) {

                    if (strpos($or, 'and'))
                        $ands = explode('and', $or);

                    if ($this->isLoopable($ands)) {

                        $and = true;
                        $position = array();

                        foreach ($ands as $an) {

                            if ($and)
                                if (!strpos($stack, $an)) {

                                    $and = false;
                                    $position[$an][$stack] = strpos($stack, $an);
                                }
                        }

                        if ($and) {

                            $result[$or][$stack] = $position;
                        }
                    }

                    $result[$or][$stack] = strpos($stack, $or);
                }
            }
            else{

                $ands = explode('and', $subject);

                if ($this->isLoopable($ands)) {

                    $and = true;
                    $position = array();

                    foreach ($ands as $an) {

                        if ($and)
                            if (!strpos($stack, $an)) {

                                $and = false;
                                $position[$an][$stack] = strpos($stack, $an);
                            }
                    }

                    if ($and) {

                        $result[$or][$stack] = $position;
                    }
                }

                $result[$or][$stack] = strpos($stack, $or);
            }

        }

        return $result;
    }*/

    public function ArrayToString(array $array, $separator = null){

        $string = '';

        foreach($array as $ar) $string .= $ar.$separator;

        return $string;
    }

    public function RefactorUrl($url){

        $chunks = explode('/', $url);

        $array = $chunks;

        $deleteIndex = array();

        foreach($array as $key => $urlChunk){

            if($urlChunk == '..'){

                unset($array[$key]);
                $deleteIndex[] = $key-1;
            }
            else if($urlChunk == '.') unset($array[$key]);
        }

        foreach($deleteIndex as $key){

            $array = $this->deleteIndex($array, $key);
        }

        $index = 0;
        foreach($array as $key=>$value){

            if($index != $key){

                $array[$index] = $value;
                unset($array[$key]);
            }

            $index++;
        }

        return implode('/', $array);
    }

    private function deleteIndex($array, $key){

        if(isset($array[$key])) unset($array[$key]);
        else $array = $this->deleteIndex ($array , $key-1);

        return $array;
    }

    public function GetClassFromNameSpacedClass($namespacedClass){

        $position = (strrpos($namespacedClass, '\\'))+1;

        return substr($namespacedClass, $position);
    }

    public function GetTableNameFromNameSpacedClass($namespacedClass){

        $namespaced = $this->GetClassFromNameSpacedClass($namespacedClass);

        return str_replace('Repository', '', str_replace('Entity', '', $namespaced));
    }

    public function ReturnFalse(){

        return false;
    }

    public function ReturnTrue(){

        return true;
    }
}