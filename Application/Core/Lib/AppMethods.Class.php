<?php

namespace Application\Core;



abstract class AppMethods extends ObjectManager{

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function HashPassword($password) {

        return hash(\Get::Config('Auth.Security.PasswordEncryption'), $password);
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
            $namespace = '\\Bundles\\'.$bundle[0].'\\Repositories\\';

        $bundle[1] .= 'Repository';

        return $this->GetObject($namespace.$bundle[1], $bundle[1]);
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
            $namespace = '\\Bundles\\'.$bundle[0].'\\Entities\\';

        $bundle[1] .= 'Entity';

        return $this->GetObject($namespace.$bundle[1], $bundle[1]);
    }

    /**
     *
     * @return type
     * Gets the type of the variable
     */
    public function GetType() {

        return gettype($this->variable);
    }

    public function Trim(array $list) {

        if (!empty($list))
            foreach ($list as $trim) $this->variable = trim($this->variable, $trim);

        else $this->variable = trim($this->variable);

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

        $chunks = $this->Variable($url)->RemoveDoubleOccuranceOf(array('/'))->Explode('/')->GetVariableResult();

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

            $array = $this->DeleteIndex($array, $key);
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

    private function DeleteIndex($array, $key){

        if(isset($array[$key])) unset($array[$key]);
        else $array = $this->DeleteIndex ($array , $key-1);

        return $array;
    }

    public function ReturnFalse(){

        return false;
    }

    public function ReturnTrue(){

        return true;
    }

    public function LocatePath($key)
    {
        $identifier = $this->Variable($key)->Explode('/')->GetArrayIndex(0);

        if($identifier)
        {
            // Is bundle

            $path = $this->RefactorUrl(\Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . $key);
            if(file_exists($path))
            {
                return $path;
            }
            else
            {
                echo '<b>Invalid path, File was not found: ', $path, '</b>';
            }
        }
        else
        {
            // is Root folder

            $path = $this->RefactorUrl(\Get::Config('APPDIRS.APPLICATION_FOLDER') . $key);
            if(file_exists($path))
            {
                return $path;
            }
            else
            {
                echo '<b>Invalid path, File was not found: ', $path, '</b>';
            }
        }
    }

    public function GetLocation($file)
    {
        $chunks = $this->Variable($file)->Explode('/')->GetVariableResult();
        $file['name'] = end($chunks);
        $file['path'] = dirname($file);

        return $file;
    }

    public function ObjectToArray($obj)
    {
        try{
            $array = array();

            foreach($obj as $key => $value)
                $array[$key] = $value;

            return $array;
        }
        catch(Exception $e)
        {
            trigger_error($e->getMessage());
        }
    }

    public function ArrayToObject(array $array)
    {
        try
        {
            $obj = new \stdClass();

            foreach($array as $key => $value)
                $obj->$key = $value;

            return $obj;
        }
        catch(Exception $e)
        {
            trigger_error($e->getMessage());
        }
    }
}