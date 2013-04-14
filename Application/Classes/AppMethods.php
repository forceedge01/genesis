<?php

class AppMethods extends Debugger {

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

        if (isset($param) && (is_array($param) || is_object($param)))
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

}