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
     * @param type $bundleColonEntityName
     * @return \Application\Core\Repositories\ApplicationRepository
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetRepository($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Core\\Repositories\\';
        else
            $namespace = '\\Application\\Bundles\\'.$bundle[0].'\\Repositories\\';

        $bundle[1] .= 'Repository';

        return $this->getObject($namespace.$bundle[1]);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \bundle returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetEntity($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Core\\Entities\\';
        else
            $namespace = '\\Application\\Bundles\\'.$bundle[0].'\\Entities\\';

        $bundle[1] .= 'Entity';

        return $this->getObject($namespace.$bundle[1]);
    }

    public function Variable($var) {

        $this->variable = $var;

        return $this;
    }

    /**
     *
     * @param loopable (array, object) $list
     * @return mixed
     * Finds the variable in the list
     */
    public function Has(array $list) {

        if($this->isLoopable($list)){
            foreach ($list as $value) {

                if($this->isLoopable($value))
                    $this->IsContainedBy ($value);
                else
                    if (strstr($this->variable, $value))
                        return $this;
            }
        }
        else{
            return strstr($this->variable, $list);
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

    public function Equals($value) {

        if ($this->variable == $value)
            return $this;

        return false;
    }

    public function NotEqualsTo($list) {

        if($this->isLoopable($list))
            foreach ($list as $value) {

                if($this->isLoopable($value)) $this->contains ($value);

                else if ($this->variable != $value) return $value;
            }

        return false;
    }

    public function JsonEncode() {

        $this->variable = json_encode($this->variable);

        return $this;
    }

    public function JsonDecode() {

        $this->variable = json_decode($this->variable);

        return $this;
    }

    public function TypeCastTo($type) {

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

    public function GetVariableResult() {

        return $this->variable;
    }

    /**
     *
     * @return type
     * Gets the type of the variable
     */
    public function GetType() {

        return gettype($this->variable);
    }

    /**
     *
     * @param array $list
     * @return \AppMethods
     * Removed double occurance of substrings provided in the array
     */
    public function RemoveDoubleOccuranceOf(array $list) {

        foreach ($list as $char) $this->variable = str_replace($char . $char, $char, $this->variable);

        return $this;
    }

    public function Trim(array $list) {

        if (!empty($list))
            foreach ($list as $trim) $this->variable = trim($this->variable, $trim);

        else $this->variable = trim($this->variable);

        return $this;
    }

    /**
     *
     * @return \AppMethods
     * Remove first character from the variable
     */
    public function RemoveFirstCharacter() {

        $this->variable = substr($this->variable, 1);

        return $this;
    }

    /**
     *
     * @return \AppMethods
     * Remove last character from the variable
     */
    public function RemoveLastCharacter() {

        $this->variable = substr($this->variable, 0, -1);

        return $this;
    }

    /**
     *
     * @param array $keyedList
     * @return \AppMethods
     * Replace multiple keywords provided in a keyed array
     */
    public function Replace(array $keyedList) {

        foreach ($keyedList as $search => $replace) $this->variable = str_replace($search, $replace, $this->variable);

        return $this;
    }

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