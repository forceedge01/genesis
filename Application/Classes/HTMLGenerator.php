<?php

class HTMLGenerator extends Router{

    private $formname;//used for saving and retrieving form from database
    private $form, $errors = array(), $errorColor, $elements = array();
    private $name, $method, $action;
    private $host, $database, $username, $password, $link;

    function __construct($params = null){
            $this->formname = $this->name = $params['name'];
            $this->method = $params['method'];
            $this->action = $params['action'];
            $this->init($params);
            $this->form = "<form method='$#method' ".(@$params['enctype'] == true ? "enctype='multipart/form-data'" : null)." name='$#name' action='$#action' id = '".@$params['id']."' class='".@$params['class']."' style='".(@$params['align'] == 'center' ? 'margin:0px auto;' : (@$params['align'] == 'right' ? 'float: right;' : (@$params[''] == 'left' ? 'float:left;' : null)))." ".(isset($params['width']) ? 'width: '.$params['width'].';' : null)." ".@$params['style']."'>";
            $this->color = 'red';
    }

    private function init($params = null){//create table in database if you want the forms to work with save, recall and erase methods
    try{
            if(!empty($params['host']) && !empty($params['username'])){
                    //create table for database here
                    $this->host = $params['host'];
                    $this->database = $params['database'];
                    $this->username = $params['username'];
                    $this->password = $params['password'];
                    if($this->database == null){//create a db for this guy and use it
                            $dbname = $this->rand_string(12);
                            $this->database = $dbname;
                            $mysqli = $this->link = new mysqli($this->host, $this->username, $this->password);
                            if ($mysqli->connect_error) {
                                    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
                            }
                            $sql = "CREATE DATABASE IF NOT EXISTS $this->database";
                            if (!$mysqli->query($sql)){
                                    echo "Error creating database: " . mysql_error();
                                    exit();
                            }
                            else{
                                    $mysqli->select_db($this->database);
                            }
                    }
                        $sql= 'DESC '.$this->formname.'Data;';
                    mysql_query($sql,$con);
                    if (mysql_errno()==1146){
                            $sql = "CREATE TABLE {$this->forname}Data(
                            id int(9) auto_incremenet,
                            elements TEXT,
                            primary key (id)
                    )";

                    }
                    elseif (!mysql_errno()){
                            //table exist
                            $sql = "SELECT
                                    column_name,
                                    column_type    # or data_type
                                    FROM information_schema.columns
                                    WHERE table_name='{$this->forname}Data';";
                            $result = $mysqli->query($sql);
                            $this->pre($result);
                            //$this->render();
                    }

                    $mysqli->query($sql);
                    $mysqli->close();
            }
            }
            catch(Exception $e){
                    echo 'This is an exception from function init';
                    pre($e);
            }
    }

    public function __get($property){
            if(property_exists($this, $property)){
                    return $this->$property;
            }
    }

    public function __set($property, $value){
            if(property_exists($this, $property)){
                    $this->$property = $value;
            }
            return $this;
    }

    public function generateInput($element){

        $element = $this->add($element);

        return $element;
    }

    public function generateForm($input){

        $form .= '<div class="widget">
            <div class="title"><h6>'.@($input['title'] ? $input['title'] : 'Default title' ).'</h6></div>
            <form method="'.@(($input['method']) ? $input['method'] : 'POST' ).'" message="'.@$input['message'].'" action="'.$this->setRoute($input['action']).'" enctype="'.$input['enctype'].'" class="'.@(($input['class']) ? $input['class'] : 'form' ).'" id="'.$input['id'].'">
            <fieldset>';

        foreach($input['inputs'] as $key => $element){

            if($key != 'hidden')
                $form .= '<div class="formRow">';

            $element['type'] = $key;

            if($key != 'hidden')
                $form .= '<label for="'.@$element['id'].'">'.@$element['label'].'</label>
                    <div class="rowRight">';

            $form .= $this->generateInput($element);

            if($key != 'hidden')
                $form .= '</div></div>';
        }

        $form .= '</fieldset><div class="wizButtons"><div class="wNavButtons">';

        foreach($input['submission'] as $key => $submit){

            $submit['type'] = $key;

            $form .= $this->generateInput($submit);
        }

        $form .= '</div></div></form></div>';

        return $form;
    }





    function startSection($params = array()){
            $this->form .= '<div class = "formBuilder_section '.@$params['class'].' formBuilder'.@$params['align'].'" id = "'.@$params['id'].'">';
    }

    function endSection(){
            $this->form .= '</div>';
    }

    function flush(){
            $this->form = null;
    }

    function save(){//use to save form in its current state and continue building it on another page
            //save the added fields
    }

    function erase(){//delete the form history saved by the save method
            //delete * from database
    }

    function recall(){//recall the saved data from the database
            //get data from the database
    }

    function add($params){//this function should store data in an array so that they can be re-arranged or modified individually.

    $i = 0;

    switch($params['type']){
            case 'text':
                    {
                            $element .= '
                            <!-- input -->
                            <input type="text" class="'.@$params['class'];
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= ' value="'.@$params['value'].'" name="'.$params['name'].'"
                            id="'.@$params['id'].'">
                            <!-- end input -->
                            ';
                            break;
                    }
            case 'textarea':
                    {
                            $element .= '
                            <!-- textarea -->
                            <textarea cols="" rows="" class="'.@$params['class'];
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= ' name="'.$params['name'].'"
                            id="'.@$params['id'].'">'.@$params['value'].'</textarea>
                            <!-- end textarea -->
                            ';
                            break;
                    }
            case 'imageUpload':
                    {
                            $element .= '
                            <!-- input -->
                            <input type="text" class="fittosize '.@$params['class'].' '.@$params['validation'].'"';
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= ' value="'.@$params['value'].'" name="'.$params['name'].'"
                            id="'.@$params['id'].'">
                            <input type="button" value="'.@$params['buttonvalue'].'" style="'.@$params['buttonstyle'].'" id="'.@$params['buttonid'].'" class="'.@$params['buttonclass'].'" >
                            </div>
                            <!-- end input -->
                            ';
                            break;
                    }
            case 'select':
                    {
                           $elements = explode(',', @$params['value']);
                           $element .= '
                            <!-- select elemenet -->
                            <select name="'.$params['name'].'" id="'.@$params['id'].'"  class="'.@$params['class'].'"';
                            if(isset($params['multiple']))
                                    $element .= ' multiple="multiple" ';
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= '">';
                            if(isset($params['requiredtext']))
                                    $element .= '<option value="">'.@$params['requiredtext'].'</option>';
                            if(is_numeric($elements[0]))
                            {
                                    if($elements[0] < $elements[1]){
                                            for($j = $elements[0]; $j <= $elements[1]; $j += $elements[2])
                                            {
                                                    $element .= '<option value="'.$j.'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' selected="selected" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= '>'.@$params['prepend'].$j.@$params['append'].'</option>';
                                            }
                                    }
                                    else{
                                            for($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2])
                                            {
                                                    $element .= '<option value="'.$j.'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' selected="selected" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= '>'.@$params['prepend'].$j.@$params['append'].'</option>';
                                            }
                                    }
                            }
                            else
                            {
                                    foreach($elements as $elementname)
                                    {
                                            $element .= '<option value="'.str_replace(' ', '_', $elementname).'" ';
                                            if(isset($params['selected']) && @$params['selected']  == $elementname)
                                                    $element .= ' selected="selected" ';
                                            if(isset($params['disabled']))
                                                    $element .= ' disabled="disabled" ';
                                            $element .= '>'.@$params['prepend'].$elementname.@$params['append'].'</option>';
                                    }
                            }
                            $element .= '</select>
                            <!-- end select type -->
                            ';
                            break;
                    }
            case 'image':
                    {
                            $element .= '
                            <img align = "'.@$params['align'].'" class = "'.@$params['class'].'" id = "'.@$params['id'].'" src = "'.@$params['src'].'" title = "'.@$params['title'].'" alt = "'.@$params['alt'].'" />
                            <!-- end image -->
                            ';
                            break;
                    }
            case 'linkedImage':
                    {
                            $element .= '
                            <!-- linkedImage -->
                            <a href="'.@$params['href'].'" target = "'.@$params['target'].'"><img class = "'.@$params['class'].'" id = "'.@$params['id'].'" align = "'.@$params['align'].'" src = "'.@$params['src'].'" title = "'.@$params['title'].'" alt = "'.@$params['alt'].'"></a>
                            <!-- end linkedImage -->
                            ';
                            break;
                    }
            case 'href':
            case 'link':
                    {
                            $element .= '
                            <!-- link -->
                            <a href="'.@$params['href'].'" class = "'.@$params['class'].'" id = "'.@$params['id'].'" target = "'.@$params['target'].'">'.@$params['title'].'</a>
                            <!-- end link -->
                            ';
                            break;
                    }
            case 'radio':
                    {
                            $elements = explode(',', @$params['value']);
                            if(is_numeric($elements[0]))
                            {
                                    if($elements[0] < $elements[1])
                                    {
                                            for($j = $elements[0]; $j <= $elements[1]; $j += $elements[2])
                                            {
                                                    $element .= '<input id="'.@$params['id'].'_'.$i.'"  class="'.@$params['class'].'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' checked="checked" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= ' type="radio" name="'.$params['name'].'[]" value="'.$j.'">';
                                                    $i++;
                                            }
                                    }
                                    else
                                    {
                                            for($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2])
                                            {
                                                    $element .= '<input id="'.@$params['id'].'_'.$i.'"  class="'.@$params['class'].'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' checked="checked" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= ' type="radio" name="'.$params['name'].'[]" value="'.$j.'">';
                                                    $i++;
                                            }
                                    }
                            }
                            else
                            {
                                    foreach($elements as $elementname)
                                    {
                                            $element .= '<input id="'.@$params['id'].'_'.$i.'" class="'.@$params['class'].'"';
                                            if(isset($params['selected']) && @$params['selected'] == $elementname)
                                                    $element .= ' checked="checked" ';
                                            if(isset($params['disabled']))
                                                    $element .= ' disabled="disabled" ';
                                            $element .= ' type="radio" name="'.$params['name'].'[]" value="'.str_replace(' ','_', $elementname).'">';
                                            $i++;
                                    }
                            }

                            break;
                    }
            case 'checkbox':
                    {
                            $elements = explode(',', @$params['value']);
                            if(is_numeric($elements[0]))
                            {
                                    if($elements[0] < $elements[1]){
                                            for($j = $elements[0]; $j <= $elements[1]; $j += $elements[2])
                                            {
                                                    $element .= '<input id="'.@$params['id'].'_'.$i.'" type="checkbox"  class="'.@$params['class'].'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' checked="checked" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= 'name="'.$params['name'].'[]" value="'.$j.'">';
                                                    $i++;
                                            }
                                    }
                                    else{
                                            for($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2])
                                            {
                                                    $element .= '<input id="'.@$params['id'].'_'.$i.'" type="checkbox"  class="'.@$params['class'].'" ';
                                                    if(isset($params['selected']) && @$params['selected'] == $j)
                                                            $element .= ' checked="checked" ';
                                                    if(isset($params['disabled']))
                                                            $element .= ' disabled="disabled" ';
                                                    $element .= 'name="'.$params['name'].'[]" value="'.$j.'">';
                                                    $i++;
                                            }
                                    }
                            }
                            else
                            {
                                    foreach($elements as $elementname)
                                    {
                                            $element .= '<input id="'.@$params['id'].'_'.$i.'" type="checkbox"  class="'.@$params['class'].'" ';
                                            if(isset($params['selected']) && @$params['selected'] == $elementname)
                                                    $element .= ' checked="checked" ';
                                            if(isset($params['disabled']))
                                                    $element .= ' disabled="disabled" ';
                                            $element .= 'name="'.$params['name'].'[]" value="'.$elementname.'">';
                                            $i++;
                                    }
                            }
                            break;
                    }
            case 'slider':
                    {
                            $elements = explode(',', @$params['value']);
                            $element .= '
                            <!-- slider code -->
                            <script>
                            jQuery(document).ready(function(){
                            $(\'#'.$params['name'].'slider\').slider({
                            min: '.$elements[0].',
                            max: '.$elements[1].',
                            step: '.$elements[2].',
                            animate: true,
                            slide: function( event, ui ) {
                                    var $input = $(this).parent().prev().children(\'input\');
                                    $($input).val( ui.value );
                            }
                            });
                            });
                            </script>

                            <input type="text"';
                            if(isset($params['size']))
                                    $element .= ' size="'.@$params['size'].'" ';
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= ' class="'.@$params['class'].' digit bb_valuedinput" value="0" name="'.$params['name'].'"
                            id="'.@$params['id'].'">
                            </div>
                            <div class="bb_slidercontainer" id="">
                            <div class="" id="'.$params['name'].'slider"></div>
                            </div>
                            <!-- slider code end -->
                            ';
                            break;
                    }
            case 'datepicker':
                    {
                            $element .= '
                            <!-- datepicker with class date -->
                            <script>
                            jQuery(document).ready(function(){
                            $(\'input[name='.$params['name'].']\').datepicker({
                                    dateFormat: "dd/mm/yy",
                                    onSelect: function(){
                                    $( this ).parents(\'.bb_inputbox\').switchClass( \'error\', \'tick\', 0 );
                                    return true;
                                    }
                            });
                            });
                            </script>

                            <input type="text" class="datepicker '.@$params['class'].' date" value="" ';
                            if(isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                            $element .= ' name="'.$params['name'].'" id="'.@$params['id'].'">
                            </div>
                            <!-- end datepicker -->
                            ';
                            break;
                    }
            case 'selectdate':
                    {
                            $selected = explode(',', @$params['selected']);
                            $element .= '
                            <!-- datepicker with class date -->
                            '.@$params['htmlprepend'].'
                            <div class="bb_inputbox'.@$params['valid'].' '.@$params['icon'].'" id="">
                            <div title = "'.@$params['icontext'].'" class="bb_inputverify" id=""></div>
                            <select name="selectday'.$params['name'].'" id="selectday'.$params['name'].'" class="dobday dateselect'.@$params['valid'].' '.@$params['class'].'">
                            <option value="">Day</option>
                            ';
                            for($j = 1; $j < 32; $j++)
                            {
                                    $element .= '<option value="'.$j.'" ';
                                    if(isset($params['selected']) && $selected[0] == $j)
                                            $element .= ' selected="selected" ';
                                    $element .= '>'.@$params['prepend'].$j.@$params['append'].'</option>';
                            }
                            $element .= '</select>
                            <select name="selectmonth'.$params['name'].'" id="selectmonth'.$params['name'].'" class="dobmonth dateselect'.@$params['valid'].' '.@$params['class'].'">
                            <option value="">Month</option>
                            ';
                            for($j = 1; $j < 13; $j++)
                            {
                                    $element .= '<option value="'.$j.'" ';
                                    if(isset($params['selected']) && $selected[1] == $j)
                                            $element .= ' selected="selected" ';
                                    $element .= '>'.@$params['prepend'].$j.@$params['append'].'</option>';
                            }
                            $element .= '</select>
                            <select name="selectyear'.$params['name'].'" id="selectyear'.$params['name'].'" class="dobyear dateselect'.@$params['valid'].' '.@$params['class'].'">
                            <option value="">Year</option>
                            ';
                            for($j = 2012; $j > 1950; $j--)
                            {
                                    $element .= '<option value="'.$j.'" ';
                                    if(isset($params['selected']) && $selected[2] == $j)
                                            $element .= ' selected="selected" ';
                                    $element .= '>'.@$params['prepend'].$j.@$params['append'].'</option>';
                            }
                            $element .= '</select>
                            <label for="selectday" style="'.@$params['style'].'" class="bb_inputheading inputpositioner" id="selectday'.$params['name'].'">'.$params['title'].'<span>'.@$params['required'].'</span>
                            </label>
                            </div>
                            '.@$params['htmlappend'].'
                            <!-- end datepicker -->
                            '; break;
                    }
            case 'hidden':
                    {
                            $element .= '
                            <!-- hidden element -->
                            <input type="hidden" id="'.@$params['id'].'" value="'.@$params['value'].'" name="'.$params['name'].'">
                            <!-- end button -->
                            ';
                            break;
                    }
            case 'button':
                    {
                            $element .= '
                            <!-- button -->
                            <input type="button" value="'.@$params['value'].'" id="'.@$params['id'].'" class="'.@$params['class'].'" >
                            <!-- end button -->
                            ';
                            break;
                    }
            case 'postcode':
                    {
                            $element .= '
                            <!-- postcode -->
                                    '.@$params['htmlprepend'].'
                                            <div class="bb_inputbox'.@$params['valid'].' '.@$params['icon'].'" id="">
                                                    <div title = "'.@$params['icontext'].'" class="bb_inputverify" id="" style="display: none;"></div>
                                                    <input style="display: none;" type="text" class="postcodeinput '.@$params['class'].'" value="'.@$params['value'].'"
                                                            name="'.$params['name'].'" id="postcode"> <label for="postcode"
                                                            class="bb_inputheading inputpositioner" id="">'.$params['title'].'<span>'.@$params['required'].'</span>
                                                    </label>
                                            </div>
                                    '.@$params['htmlappend'].'
                            <!-- end postcode -->
                            ';
                            break;
                    }
            case 'submit':
                    {
                            $element .= '
                            <!-- SUBMIT BUTTON -->
                            <input type = "submit" class = "'.@$params['class'].'" id = "'.@$params['id'].'" name = "'.$params['name'].'" value = "'.@$params['value'].'">
                            <!-- END SUBMIT BUTTON -->
                            ';
                            break;
                    }
            case 'reset':
                    {
                            $element .= '
                            <!-- RESET BUTTON -->
                            <input type = "reset" class = "'.@$params['class'].'" id = "'.@$params['id'].'" name = "'.$params['name'].'" value = "'.@$params['value'].'">
                            <!-- END Reset BUTTON -->
                            ';
                            break;
                    }
            case 'file':
                    {
                            $element .= '
                            <!-- FILE UPLOAD -->
                            <input type = "file" class = "'.@$params['class'].'" id = "'.@$params['class'].'" name = "'.$params['name'].'" value = "'.@$params['value'].'">
                            <!-- END FILE UPLOAD -->
                            ';
                            break;
                    }
            case 'iframe':
                    {
                            $element .= '
                            <!-- START IFRAME -->
                                    <iframe frameborder="0" src="'.$params['src'].'" id="'.$params['id'].'"></iframe>
                            <!-- END IFRAME CODE -->
                            ';
                    }
            default:
                    {
                            $this->form .= @$params['content'];
                    }
    }

    return $element;
//    $this->form .= $element.' </div>';
    }

    function submit(){

    }

    function validate(){

    }

    public function dump($var = null){
            $dump = $this->filterForRender($var);
            $dump = str_replace('<', '&lt;', $dump);
            $dump = str_replace('>', '&gt;', $dump);
            $dump = str_replace('&lt;!--','<br /><br />&lt;!--', $dump);
            echo $dump;
            echo '&lt;/form&gt; <br /><br />';
    }

    function setError($error){
            $err = array();
            if(!is_array($error))
                    $err[] = $error;
            else
                    $err = $error;
            foreach($err as $err => $errr)
                    $this->errors[$err] = $errr;
    }

    function showError(){
            pre($this->errors);
    }

    function render(){
            if(!empty($this->errors))
                    foreach($this->errors as $errorName => $error)
                            echo '<font color="'.$this->errorColor.'">',$errorName,': ',$error,'</font><br />';
            $render = $this->filterForRender();
            echo $render;
            echo '</form>';
    }

    private function filterForRender($var = null){
            $var = ($var == null) ? $this->form : $var;
            $filtered = str_replace('$#name',$this->name, $var);
            $filtered = str_replace('$#method',$this->method, $filtered);
            $filtered = str_replace('$#action',$this->action, $filtered);
            return $filtered;
    }

    function setErrorColor($color){
            $this->errorColor = $color;
    }

    private function rand_string( $length ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = null;
    $size = strlen( $chars );
    for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
    }

    return $str;
    }

    function startFooter($align){
            $this->form .= '<div class="formBuilderFooter formContent'.$align.'">';
    }

    function endFooter(){
            $this->form .= '</div>';
    }
    function addVariable($name){
            $this->form .= '<#'.$name.'>';
    }

    function useVariable($varName, $var){
            $this->form = str_replace('<#'.$varName.'>', $var, $this->form);
    }
}