<?php

namespace Application\Core\Lib;



use Application\Core\Interfaces\AppMethodsInterface;

abstract class AppMethods extends ObjectManager implements AppMethodsInterface {

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function HashPassword($password) {

        return hash(\Get::Config('Auth.Security.PasswordEncryption'), $password);
    }

    public function AbsolutePathToUrl($path)
    {
        return str_replace(ROOT, HOST, $path);
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

        $chunks = explode('/', str_replace('//', '/', $url));
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

    /**
     *
     * @param array $array
     * @return \stdClass
     *
     * Converts an array to an object type
     */
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

    public function ForwardTo($route, $queryString = null)
    {
        return $this->getComponent('Router')->ForwardTo($route, $queryString);
    }

    public function ForwardToController($controller, array $args = array())
    {
        return $this->getComponent('Router')->ForwardToController($controller, $args);
    }

    public function IncludeHeaderFooter()
    {
        return $this->getComponent('TemplateHandler')->IncludeHeaderAndFooter(get_called_class());
    }

    public function Render($view, $title, array $params = array())
    {
        return $this->getComponent('TemplateHandler')->Render($view, $title, $params);
    }
}