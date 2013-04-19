<?php

class AppMethods extends Debugger {

    private
        $variable;

    /**
     *
     * @param string $object
     * @param mixed $args
     * @return object $this
     * Returns an existing object or creates a new one if it does not exist in the current scope
     */
    public function getObject($object, $args = null) {

        if (!isset($this->$object) && !is_object($this->$object)) {

            if (class_exists($object)) {

                $this->$object = new $object($args);
                $this->$object->objectCreatedAt = time();
            }
            else
                trigger_error("getOjbect accepts valid class name only, $object class does not exist", E_USER_ERROR);
        }

        return $this->$object;
    }

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function hashPassword($password) {

        return hash(AUTH_PASSWORD_ENCRYPTION_ALGORITHM, $password);
    }

    /**
     *
     * @param int $length: length of the string to generate.
     * @return a random string generated equals the length specified
     */
    public function generateRandomString($length = 10) {

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
    public function stripDoubleSlashes($string) {

        return str_replace('//', '/', $string);
    }

    /**
     *
     * @param mixed $param
     * @return boolean Checks if a variable is loopable
     */
    public function isLoopable($param) {

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
    public function find($subject, $haystack) {

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
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \bundle returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function getBundleEntity($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        $path = BUNDLES_FOLDER . $bundle[0] . '/Entities/'.$bundle[1].'Entity.php';

        if(is_file(BUNDLES_FOLDER . $bundle[0] . '/Entities/'.$bundle[1].'Entity.php'))
            require_once BUNDLES_FOLDER . $bundle[0] . '/Entities/'.$bundle[1].'Entity.php';
        else{

            echo 'Entity ' . $bundle[1] . ' not found at ' . $path;
            exit;
        }

        return new $bundle[1]();
    }

    public function variable($var) {

        $this->variable = $var;

        return $this;
    }

    /**
     *
     * @param loopable (array, object) $list
     * @return mixed
     * Finds the variable in the list
     */
    public function has($list) {

        if($this->isLoopable($list))
            foreach ($list as $value) {

                if($this->isLoopable($value))
                    $this->IsContainedBy ($value);
                else
                    if (strstr($this->variable, $value))
                        return $this;
            }

        return false;
    }

    /**
     *
     * @param loopable (array, object) $list
     * @return mixed
     * Finds the variable in the list
     */
    public function IsIn($list, $flag = false) {

        if($flag)
            return true;

        if($this->isLoopable($list)){

            $flag = false;

            foreach ($list as $value) {

                if($this->isLoopable($value)){

                    $flag = $this->IsIn ($value, $flag);

                    if($flag)
                        return true;

                }
                else{

                    if (strstr($value, $this->variable)){

                        $flag = true;
                    }
                }
            }
        }
        else{

            if (strstr($list, $this->variable)){

                $flag = true;
            }
        }

        return $flag;
    }

    public function equals($value) {

        if ($this->variable == $value)
            return $this;

        return false;
    }

    public function notEqualsTo($value) {

        if($this->isLoopable($list))
            foreach ($list as $value) {

                if($this->isLoopable($value))
                    $this->contains ($value);
                else
                    if ($this->variable != $value)
                        return $value;
            }

        return false;
    }

    public function jsonEncode() {

        $this->variable = json_encode($this->variable);

        return $this;
    }

    public function jsonDecode() {

        $this->variable = json_decode($this->variable);

        return $this;
    }

    public function typeCastTo($type) {

        $type = strtolower($type);

        switch ($type) {

            case 'int':
            case 'integer':
                $this->variable = (int) $this->variable;
                break;
            case 'float':
            case 'double':
                $this->variable = (double) $this->variable;
                break;
            case 'string':
            case 'char':
            case 'text':
                $this->variable = (string) $this->variable;
                break;
        }

        return $this;
    }

    public function getVariableResult() {

        return $this->variable;
    }

    /**
     * 
     * @return type
     * Gets the type of the variable
     */
    public function getType() {

        return gettype($this->variable);
    }

    /**
     * 
     * @param array $list
     * @return \AppMethods
     * Removed double occurance of substrings provided in the array
     */
    public function removeDoubleOccuranceOf(array $list) {

        foreach ($list as $char) {

            $this->variable = str_replace($char . $char, $char, $this->variable);
        }

        return $this;
    }

    public function trim(array $list) {

        if (!empty($list))
            foreach ($list as $trim) {

                $this->variable = trim($this->variable, $trim);
            }
        else
            $this->variable = trim($this->variable);

        return $this;
    }

    /**
     * 
     * @return \AppMethods
     * Remove first character from the variable
     */
    public function removeFirstCharacter() {

        $this->variable = substr($this->variable, 1);

        return $this;
    }

    /**
     * 
     * @return \AppMethods
     * Remove last character from the variable
     */
    public function removeLastCharacter() {

        $this->variable = substr($this->variable, 0, -1);

        return $this;
    }

    /**
     * 
     * @param array $keyedList
     * @return \AppMethods
     * Replace multiple keywords provided in a keyed array
     */
    public function replace(array $keyedList) {

        foreach ($keyedList as $search => $replace) {

            $this->variable = str_replace($search, $replace, $this->variable);
        }

        return $this;
    }
}