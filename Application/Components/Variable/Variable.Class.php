<?php

namespace Application\Core;

/**
 * Author: Wahab Qureshi.
 */

abstract class Variable extends Hooks {

    private
        $variable;

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
            foreach ($list as $value)
            {

                if($this->isLoopable($value))
                {
                    $this->IsIn ($value);
                }
                else
                {   if ($this->variable == $value)
                        return $this;
                }
            }
        }
        else
        {
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

    public function RemoveValue($value)
    {
        for($i = 0; $i < count($this->variable); $i++)
        {
            if($this->variable[$i] == $value)
            {
                unset($this->variable[$i]);
                $this->variable = array_values($this->variable);
            }
        }

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

    protected function FirstToUpper()
    {
        $this->variable = strtoupper(substr($this->variable, 0, 1)).substr($this->variable, 1);
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

    public function IsEmpty(){

        if(empty($this->variable))
            return $this;
        else
            return false;
    }

    public function IsNotEmpty(){

        if(!empty($this->variable)) return $this;
        else return false;
    }

    public function ToLower(){

        $this->variable = strtolower($this->variable);
        return $this;
    }

    public function ToUpper(){

        $this->variable = strtoupper($this->variable);
        return $this;
    }

    public function IsNumber(){

        if(is_numeric($this->variable)) return $this;
        else return false;
    }

    public function IsString(){

        if(is_string($this->variable)) return $this;
        else return false;
    }

    public function IsArray(){

        if(is_array($this->variable)) return $this;
        else return false;
    }

    public function IsObject(){

        if(is_object($this->variable)) return $this;
        else return false;
    }

    public function IsBool(){

        if(is_bool($this->variable)) return $this;
        else return false;
    }

    public function IsCallable($property = null){

        if ( empty ( $property ))
            if(is_callable($this->variable)) return $this;
        else if (!empty ( $property ) )
            if (is_callable($this -> variable -> $property)) return $this;
        else return false;
    }

    public function IsDir(){

        if(is_dir($this->variable)) return $this;
        else return false;
    }

    public function IsFloat(){

        if(is_float($this->variable)) return $this;
        else return false;
    }

    public function IsFile(){

        if(is_file($this->variable)) return $this;
        else return false;
    }

    public function IsNotANumber(){

        if(is_nan($this->variable)) return $this;
        else return false;
    }

    public function IsFileReadable(){

        if(is_readable($this->variable)) return $this;
        else return false;
    }

    public function IsFileWritable(){

        if(is_writable($this->variable)) return $this;
        else return false;
    }

    public function GetObjectProperty($property){

        return $this->variable->$property;
    }

    public function PropertyEquals ( $property, $equals){

        if ( $this -> variable -> $property == $equals)
            return $this;
        else
            return false;
    }

    public function IndexEquals ( $index, $equals){

        if ( $this -> variable[$index] == $equals)
            return $this;
        else
            return false;
    }

    public function PropertyIsNotEqualTo ( $property, $equals){

        if ( $this -> variable -> $property != $equals)
            return $this;
        else
            return false;
    }

    public function IndexIsNotEqualTo ( $index, $equals){

        if ( $this -> variable[$index] != $equals)
            return $this;
        else
            return false;
    }

    public function GetArrayIndex($key){

        return $this->variable[$key];
    }

    public function GetLastArrayIndex()
    {
        return end($this->variable);
    }

    public function CallMethod($method){

        return $this->variable->$method();
    }

    public function IsGreaterThan($number){

        if($this->variable > $number) return $this;
        else return false;
    }

    public function IsSmallerThan($number){

        if($this->variable < $number) return $this;
        else return false;
    }

    public function IsGreaterOrEqualTo($number){

        if($this->variable >= $number) return $this;
        else return false;
    }

    public function IsSmallerOrEqualTo($number){

        if($this->variable <= $number) return $this;
        else return false;
    }

    public function GoToObjectProperty($property){

        $this->variable = $this->variable->$property;
        return $this;
    }

    public function GoToArrayIndex($index){

        $this->variable = $this->variable[$index];
        return $this;
    }

    public function Explode($delimiter){

        $this->variable = explode($delimiter, $this->variable);
        return $this;
    }

    public function Implode($glue){

        $this->variable = implode($glue, $this->variable);
        return $this;
    }

    public function ReplaceInEach(array $keyedList){

        $list = array();

        foreach($this->variable as $key => $val)
            foreach($keyedList as $search => $replace)
                $list[$key] = str_replace($search, $replace, $val);

        $this->variable = $list;

        return $this;
    }

    public function Assign($value){

        $this->variable = $value;
        return $this;
    }

    public function ArithmaticAdd($value){

        $this->variable += $value;
        return $this;
    }

    public function ArithmaticSubtract($value){

        $this->variable -= $value;
        return $this;
    }

    public function ArithmaticDivide($value){

        $this->variable /= $value;
        return $this;
    }

    public function ArithmaticMultiply($value){

        $this->variable *= $value;
        return $this;
    }

    public function ArithmaticIsModOf($value){

        if($this->variable % $value == 0) return $this;

        else return false;
    }

    public function Concat(array $string){

        foreach($string as $val)
            $this->variable .= $val;
        return $this;
    }

    public function RemoveOccuranceOf(array $string){

        foreach($string as $value){

            $list[$value] = '';
        }

        $this->Replace($list);
        return $this;
    }

    public function Search($value)
    {
        return array_search($value, $this->variable);
    }
}