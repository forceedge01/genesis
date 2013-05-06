<?php

namespace Application\Components;

use Application\Core\Router;

class HTMLGenerator extends Router {

    private $formname; //used for saving and retrieving form from database
    private $form, $errors = array(), $errorColor, $elements = array(), $element = array();
    private $name, $method, $action;
    private $host, $database, $username, $password, $link;

    function __construct($params = null) {
        $this->formname = $this->name = $params['name'];
        $this->method = $params['method'];
        $this->action = $params['action'];
        $this->init($params);
        $this->form = "<form method='$#method' " . (@$params['enctype'] == true ? "enctype='multipart/form-data'" : null) . " name='$#name' action='$#action' id = '" . @$params['id'] . "' class='" . @$params['class'] . "' style='" . (@$params['align'] == 'center' ? 'margin:0px auto;' : (@$params['align'] == 'right' ? 'float: right;' : (@$params[''] == 'left' ? 'float:left;' : null))) . " " . (isset($params['width']) ? 'width: ' . $params['width'] . ';' : null) . " " . @$params['style'] . "'>";
        $this->color = 'red';
    }

    private function init($params = null) {//create table in database if you want the forms to work with save, recall and erase methods
        try {
            if (!empty($params['host']) && !empty($params['username'])) {
                //create table for database here
                $this->host = $params['host'];
                $this->database = $params['database'];
                $this->username = $params['username'];
                $this->password = $params['password'];
                if ($this->database == null) {//create a db for this guy and use it
                    $dbname = $this->rand_string(12);
                    $this->database = $dbname;
                    $mysqli = $this->link = new mysqli($this->host, $this->username, $this->password);
                    if ($mysqli->connect_error) {
                        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                    }
                    $sql = "CREATE DATABASE IF NOT EXISTS $this->database";
                    if (!$mysqli->query($sql)) {
                        echo "Error creating database: " . mysql_error();
                        exit();
                    } else {
                        $mysqli->select_db($this->database);
                    }
                }
                $sql = 'DESC ' . $this->formname . 'Data;';
                mysql_query($sql, $con);
                if (mysql_errno() == 1146) {
                    $sql = "CREATE TABLE {$this->forname}Data(
                            id int(9) auto_incremenet,
                            elements TEXT,
                            primary key (id)
                    )";
                } elseif (!mysql_errno()) {
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
        } catch (Exception $e) {
            echo 'This is an exception from function init';
            pre($e);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }

    public function generateInput($element) {

        $element = $this->add($element);

        return $element;
    }

    public function generateForm($input) {

        $formHeader = function() use ($input){

            $form = '<div class="widget">
            <div class="title"><h6>' . @($input['title'] ? $input['title'] : 'Default title' ) . '</h6></div>
            <form method="' . @(($input['method']) ? $input['method'] : 'POST' ) . '" message="' . @$input['message'] . '" action="' . @$input['action'] . '" enctype="' . @$input['enctype'] . '" class="' . @(($input['class']) ? $input['class'] : 'form' ) . '" id="' . @$input['id'] . '">
            <fieldset>';

            return $form;
        };

        $formBody = function($form = null) use ($input){

            if (isset($input['table']))
                $form .= $this->DBForm($input['table']);

            foreach ($input['inputs'] as $key => $element) {

                if ($key != 'hidden')
                    $form .= '<div class="formRow">';

                $element['type'] = $key;

                if ($key != 'hidden')
                    $form .= '<label for="' . @$element['id'] . '">' . @$element['label'] . '</label>
                        <div class="formRight">';

                $form .= $this->generateInput($element);

                if ($key != 'hidden')
                    $form .= '</div></div>';
            }

            $form .= '</fieldset>';

            return $form;
        };

        $formFooter = function() use ($input){

            $form = '<div class="wizButtons"><div class="wNavButtons">';

            foreach ($input['submission'] as $key => $submit) {

                $submit['type'] = $key;

                $form .= $this->generateInput($submit);
            }

            $form .= '</div></div></form></div>';

            return $form;

        };

        return $formHeader() . $formBody() . $formFooter();
    }

    protected function DBForm($inputs) {

        $html = null;

        if ($this->isLoopable($inputs[0])) {

            foreach ($inputs[0] as $key => $input) {

                $html .= '<div class="formRow">';
                $element['value'] = $input;
                $element['name'] = $key;
                $element['label'] = $this->FirstCharacterToUpperCase($key);

                if (is_numeric($input)) {

                    $element['type'] = 'text';
                    $element['value'] = $input;
                } else if (is_array($input)) {

                    $element['type'] = 'text';
                } else if (strlen($input) > 70) {

                    $element['type'] = 'textarea';
                } else if (is_string($input)) {

                    $element['type'] = 'text';
                }

                $html .= '<label for="' . @$element['id'] . '">' . @str_replace('_', ' ', $element['label']) . '</label>
                    <div class="formRight">';

                $html .= $this->generateInput($element);

                $html .= '</div></div>';
            }

        } else {

            foreach ($inputs as $key => $value) {

                $html .= '<div class="title"><h6>' . $key . '</h6></div>';

                foreach ($value as $column) {

                    if ($column->Extra != 'auto_increment') {

                        $html .= '<div class="formRow">';
                        $element['name'] = str_replace('.', '__', $column->Field);
                        $element['label'] = $this->FirstCharacterToUpperCase(str_replace('`', '', end(explode('.', $column->Field))));
                        $element['id'] = str_replace('.', '_', $column->Field);

                        if (strpos($column->Type, 'int') > 0) {

                            $element['type'] = 'text';
                        } else if (strpos($column->Type, 'varchar') > 0) {

                            $element['type'] = 'text';
                        } else if (strpos($column->Type, 'enum') > 0) {

                            $element['type'] = 'select';
                        } else {

                            $element['type'] = 'text';
                        }

                        $html .= '<label for="' . @$element['name'] . '">' . @str_replace('_', ' ', $element['label']) . '</label>
                            <div class="formRight">';

                        $html .= $this->generateInput($element);

                        $html .= '</div></div>';
                    }
                }
            }
        }


        return $html;
    }

    function startSection($params = array()) {
        $this->form .= '<div class = "formBuilder_section ' . @$params['class'] . ' formBuilder' . @$params['align'] . '" id = "' . @$params['id'] . '">';
    }

    function endSection() {
        $this->form .= '</div>';
    }

    function flush() {
        $this->form = null;
    }

    function save() {//use to save form in its current state and continue building it on another page
        //save the added fields
    }

    function erase() {//delete the form history saved by the save method
        //delete * from database
    }

    function recall() {//recall the saved data from the database
        //get data from the database
    }

    function add($params) {//this function should store data in an array so that they can be re-arranged or modified individually.
        $i = 0;

        $element = null;

        switch ($params['type']) {

            case 'text': {
                    $element .= '
                            <!-- input -->
                            <input type="text" class="' . @$params['class'] . '"';
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= ' value="' . @$params['value'] . '" name="' . $params['name'] . '"
                            id="' . @$params['id'] . '">
                            <!-- end input -->
                            ';
                    break;
                }

            case 'textarea': {
                    $element .= '
                            <!-- textarea -->
                            <textarea cols="" rows="" class="' . @$params['class'];
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= ' name="' . $params['name'] . '"
                            id="' . @$params['id'] . '">' . @$params['value'] . '</textarea>
                            <!-- end textarea -->
                            ';
                    break;
                }

            case 'imageUpload': {
                    $element .= '
                            <!-- input -->
                            <input type="text" class="fittosize ' . @$params['class'] . ' ' . @$params['validation'] . '"';
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= ' value="' . @$params['value'] . '" name="' . $params['name'] . '"
                            id="' . @$params['id'] . '">
                            <input type="button" value="' . @$params['buttonvalue'] . '" style="' . @$params['buttonstyle'] . '" id="' . @$params['buttonid'] . '" class="' . @$params['buttonclass'] . '" >
                            </div>
                            <!-- end input -->
                            ';
                    break;
                }

            case 'select': {
                    $elements = explode(',', @$params['value']);
                    $element .= '
                            <!-- select elemenet -->
                            <select name="' . $params['name'] . '" id="' . @$params['id'] . '"  class="' . @$params['class'] . '"';
                    if (isset($params['multiple']))
                        $element .= ' multiple="multiple" ';
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= '">';
                    if (isset($params['requiredtext']))
                        $element .= '<option value="">' . @$params['requiredtext'] . '</option>';
                    if (is_numeric($elements[0])) {
                        if ($elements[0] < $elements[1]) {
                            for ($j = $elements[0]; $j <= $elements[1]; $j += $elements[2]) {
                                $element .= '<option value="' . $j . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' selected="selected" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= '>' . @$params['prepend'] . $j . @$params['append'] . '</option>';
                            }
                        }
                        else {
                            for ($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2]) {
                                $element .= '<option value="' . $j . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' selected="selected" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= '>' . @$params['prepend'] . $j . @$params['append'] . '</option>';
                            }
                        }
                    }
                    else {
                        foreach ($elements as $elementname) {
                            $element .= '<option value="' . str_replace(' ', '_', $elementname) . '" ';
                            if (isset($params['selected']) && @$params['selected'] == $elementname)
                                $element .= ' selected="selected" ';
                            if (isset($params['disabled']))
                                $element .= ' disabled="disabled" ';
                            $element .= '>' . @$params['prepend'] . $elementname . @$params['append'] . '</option>';
                        }
                    }
                    $element .= '</select>
                            <!-- end select type -->
                            ';
                    break;
                }

            case 'image': {
                    $element .= '
                            <img align = "' . @$params['align'] . '" class = "' . @$params['class'] . '" id = "' . @$params['id'] . '" src = "' . @$params['src'] . '" title = "' . @$params['title'] . '" alt = "' . @$params['alt'] . '" />
                            <!-- end image -->
                            ';
                    break;
                }

            case 'linkedImage': {
                    $element .= '
                            <!-- linkedImage -->
                            <a href="' . @$params['href'] . '" target = "' . @$params['target'] . '"><img class = "' . @$params['class'] . '" id = "' . @$params['id'] . '" align = "' . @$params['align'] . '" src = "' . @$params['src'] . '" title = "' . @$params['title'] . '" alt = "' . @$params['alt'] . '"></a>
                            <!-- end linkedImage -->
                            ';
                    break;
                }

            case 'href':
            case 'link': {
                    $element .= '
                            <!-- link -->
                            <a href="' . @$params['href'] . '" class = "' . @$params['class'] . '" id = "' . @$params['id'] . '" target = "' . @$params['target'] . '">' . @$params['title'] . '</a>
                            <!-- end link -->
                            ';
                    break;
                }

            case 'radio': {
                    $elements = explode(',', @$params['value']);
                    if (is_numeric($elements[0])) {
                        if ($elements[0] < $elements[1]) {
                            for ($j = $elements[0]; $j <= $elements[1]; $j += $elements[2]) {
                                $element .= '<input id="' . @$params['id'] . '_' . $i . '"  class="' . @$params['class'] . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' checked="checked" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= ' type="radio" name="' . $params['name'] . '[]" value="' . $j . '">';
                                $i++;
                            }
                        }
                        else {
                            for ($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2]) {
                                $element .= '<input id="' . @$params['id'] . '_' . $i . '"  class="' . @$params['class'] . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' checked="checked" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= ' type="radio" name="' . $params['name'] . '[]" value="' . $j . '">';
                                $i++;
                            }
                        }
                    }
                    else {
                        foreach ($elements as $elementname) {
                            $element .= '<input id="' . @$params['id'] . '_' . $i . '" class="' . @$params['class'] . '"';
                            if (isset($params['selected']) && @$params['selected'] == $elementname)
                                $element .= ' checked="checked" ';
                            if (isset($params['disabled']))
                                $element .= ' disabled="disabled" ';
                            $element .= ' type="radio" name="' . $params['name'] . '[]" value="' . str_replace(' ', '_', $elementname) . '">';
                            $i++;
                        }
                    }

                    break;
                }

            case 'checkbox': {
                    $elements = explode(',', @$params['value']);
                    if (is_numeric($elements[0])) {
                        if ($elements[0] < $elements[1]) {
                            for ($j = $elements[0]; $j <= $elements[1]; $j += $elements[2]) {
                                $element .= '<input id="' . @$params['id'] . '_' . $i . '" type="checkbox"  class="' . @$params['class'] . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' checked="checked" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= 'name="' . $params['name'] . '[]" value="' . $j . '">';
                                $i++;
                            }
                        }
                        else {
                            for ($j = $elements[1]; $j >= $elements[0]; $j -= $elements[2]) {
                                $element .= '<input id="' . @$params['id'] . '_' . $i . '" type="checkbox"  class="' . @$params['class'] . '" ';
                                if (isset($params['selected']) && @$params['selected'] == $j)
                                    $element .= ' checked="checked" ';
                                if (isset($params['disabled']))
                                    $element .= ' disabled="disabled" ';
                                $element .= 'name="' . $params['name'] . '[]" value="' . $j . '">';
                                $i++;
                            }
                        }
                    }
                    else {
                        foreach ($elements as $elementname) {
                            $element .= '<input id="' . @$params['id'] . '_' . $i . '" type="checkbox"  class="' . @$params['class'] . '" ';
                            if (isset($params['selected']) && @$params['selected'] == $elementname)
                                $element .= ' checked="checked" ';
                            if (isset($params['disabled']))
                                $element .= ' disabled="disabled" ';
                            $element .= 'name="' . $params['name'] . '[]" value="' . $elementname . '">';
                            $i++;
                        }
                    }
                    break;
                }

            case 'slider': {
                    $elements = explode(',', @$params['value']);
                    $element .= '
                            <!-- slider code -->
                            <script>
                            jQuery(document).ready(function(){
                            $(\'#' . $params['name'] . 'slider\').slider({
                            min: ' . $elements[0] . ',
                            max: ' . $elements[1] . ',
                            step: ' . $elements[2] . ',
                            animate: true,
                            slide: function( event, ui ) {
                                    var $input = $(this).parent().prev().children(\'input\');
                                    $($input).val( ui.value );
                            }
                            });
                            });
                            </script>

                            <input type="text"';
                    if (isset($params['size']))
                        $element .= ' size="' . @$params['size'] . '" ';
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= ' class="' . @$params['class'] . ' digit bb_valuedinput" value="0" name="' . $params['name'] . '"
                            id="' . @$params['id'] . '">
                            </div>
                            <div class="bb_slidercontainer" id="">
                            <div class="" id="' . $params['name'] . 'slider"></div>
                            </div>
                            <!-- slider code end -->
                            ';
                    break;
                }

            case 'datepicker': {
                    $element .= '
                            <!-- datepicker with class date -->
                            <script>
                            jQuery(document).ready(function(){
                            $(\'input[name=' . $params['name'] . ']\').datepicker({
                                    dateFormat: "dd/mm/yy",
                                    onSelect: function(){
                                    $( this ).parents(\'.bb_inputbox\').switchClass( \'error\', \'tick\', 0 );
                                    return true;
                                    }
                            });
                            });
                            </script>

                            <input type="text" class="datepicker ' . @$params['class'] . ' date" value="" ';
                    if (isset($params['disabled']))
                        $element .= ' disabled="disabled" ';
                    $element .= ' name="' . $params['name'] . '" id="' . @$params['id'] . '">
                            </div>
                            <!-- end datepicker -->
                            ';
                    break;
                }

            case 'selectdate': {
                    $selected = explode(',', @$params['selected']);
                    $element .= '
                            <!-- datepicker with class date -->
                            ' . @$params['htmlprepend'] . '
                            <div class="bb_inputbox' . @$params['valid'] . ' ' . @$params['icon'] . '" id="">
                            <div title = "' . @$params['icontext'] . '" class="bb_inputverify" id=""></div>
                            <select name="selectday' . $params['name'] . '" id="selectday' . $params['name'] . '" class="dobday dateselect' . @$params['valid'] . ' ' . @$params['class'] . '">
                            <option value="">Day</option>
                            ';
                    for ($j = 1; $j < 32; $j++) {
                        $element .= '<option value="' . $j . '" ';
                        if (isset($params['selected']) && $selected[0] == $j)
                            $element .= ' selected="selected" ';
                        $element .= '>' . @$params['prepend'] . $j . @$params['append'] . '</option>';
                    }
                    $element .= '</select>
                            <select name="selectmonth' . $params['name'] . '" id="selectmonth' . $params['name'] . '" class="dobmonth dateselect' . @$params['valid'] . ' ' . @$params['class'] . '">
                            <option value="">Month</option>
                            ';
                    for ($j = 1; $j < 13; $j++) {
                        $element .= '<option value="' . $j . '" ';
                        if (isset($params['selected']) && $selected[1] == $j)
                            $element .= ' selected="selected" ';
                        $element .= '>' . @$params['prepend'] . $j . @$params['append'] . '</option>';
                    }
                    $element .= '</select>
                            <select name="selectyear' . $params['name'] . '" id="selectyear' . $params['name'] . '" class="dobyear dateselect' . @$params['valid'] . ' ' . @$params['class'] . '">
                            <option value="">Year</option>
                            ';
                    for ($j = 2012; $j > 1950; $j--) {
                        $element .= '<option value="' . $j . '" ';
                        if (isset($params['selected']) && $selected[2] == $j)
                            $element .= ' selected="selected" ';
                        $element .= '>' . @$params['prepend'] . $j . @$params['append'] . '</option>';
                    }
                    $element .= '</select>
                            <label for="selectday" style="' . @$params['style'] . '" class="bb_inputheading inputpositioner" id="selectday' . $params['name'] . '">' . $params['title'] . '<span>' . @$params['required'] . '</span>
                            </label>
                            </div>
                            ' . @$params['htmlappend'] . '
                            <!-- end datepicker -->
                            ';
                    break;
                }

            case 'hidden': {
                    $element .= '
                            <!-- hidden element -->
                            <input type="hidden" id="' . @$params['id'] . '" value="' . @$params['value'] . '" name="' . $params['name'] . '">
                            <!-- end button -->
                            ';
                    break;
                }

            case 'button': {
                    $element .= '
                            <!-- button -->
                            <input type="button" value="' . @$params['value'] . '" id="' . @$params['id'] . '" class="' . @$params['class'] . '" >
                            <!-- end button -->
                            ';
                    break;
                }

            case 'postcode': {
                    $element .= '
                            <!-- postcode -->
                                    ' . @$params['htmlprepend'] . '
                                            <div class="bb_inputbox' . @$params['valid'] . ' ' . @$params['icon'] . '" id="">
                                                    <div title = "' . @$params['icontext'] . '" class="bb_inputverify" id="" style="display: none;"></div>
                                                    <input style="display: none;" type="text" class="postcodeinput ' . @$params['class'] . '" value="' . @$params['value'] . '"
                                                            name="' . $params['name'] . '" id="postcode"> <label for="postcode"
                                                            class="bb_inputheading inputpositioner" id="">' . $params['title'] . '<span>' . @$params['required'] . '</span>
                                                    </label>
                                            </div>
                                    ' . @$params['htmlappend'] . '
                            <!-- end postcode -->
                            ';
                    break;
                }

            case 'submit': {
                    $element .= '
                            <!-- SUBMIT BUTTON -->
                            <input type = "submit" class = "' . @$params['class'] . '" id = "' . @$params['id'] . '" name = "' . @$params['name'] . '" value = "' . @$params['value'] . '">
                            <!-- END SUBMIT BUTTON -->
                            ';
                    break;
                }

            case 'reset': {
                    $element .= '
                            <!-- RESET BUTTON -->
                            <input type = "reset" class = "' . @$params['class'] . '" id = "' . @$params['id'] . '" name = "' . @$params['name'] . '" value = "' . @$params['value'] . '">
                            <!-- END Reset BUTTON -->
                            ';
                    break;
                }

            case 'file': {
                    $element .= '
                            <!-- FILE UPLOAD -->
                            <input type = "file" class = "' . @$params['class'] . '" id = "' . @$params['class'] . '" name = "' . $params['name'] . '" value = "' . @$params['value'] . '">
                            <!-- END FILE UPLOAD -->
                            ';
                    break;
                }

            case 'iframe': {
                    $element .= '
                            <!-- START IFRAME -->
                                    <iframe frameborder="0" src="' . $params['src'] . '" id="' . $params['id'] . '"></iframe>
                            <!-- END IFRAME CODE -->
                            ';
                }

            default: {
                    $this->form .= @$params['content'];
                }
        }

        return $element;
//    $this->form .= $element.' </div>';
    }

    function submit() {

    }

    function validate() {

    }

    public function dump($var = null) {
        $dump = $this->filterForRender($var);
        $dump = str_replace('<', '&lt;', $dump);
        $dump = str_replace('>', '&gt;', $dump);
        $dump = str_replace('&lt;!--', '<br /><br />&lt;!--', $dump);
        echo $dump;
        echo '&lt;/form&gt; <br /><br />';
    }

    function setError($error) {
        $err = array();
        if (!is_array($error))
            $err[] = $error;
        else
            $err = $error;
        foreach ($err as $err => $errr)
            $this->errors[$err] = $errr;
    }

    function showError() {
        pre($this->errors);
    }

    function render() {
        if (!empty($this->errors))
            foreach ($this->errors as $errorName => $error)
                echo '<font color="' . $this->errorColor . '">', $errorName, ': ', $error, '</font><br />';

        echo $this->filterForRender() . '</form>';
    }

    private function filterForRender($var = null) {

        $var = ($var == null) ? $this->form : $var;

        return $this->Variable($var)->Replace([

            '$#name' => $this->name,
            '$#method' => $this->method,
            '$#action' => $this->action

        ])->GetVariableResult();
    }

    function setErrorColor($color) {

        $this->errorColor = $color;
    }

    private function rand_string($length) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = null;
        $size = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

    function startFooter($align) {
        $this->form .= '<div class="formBuilderFooter formContent' . $align . '">';
    }

    function endFooter() {
        $this->form .= '</div>';
    }

    function addVariable($name) {
        $this->form .= '<#' . $name . '>';
    }

    function useVariable($varName, $var) {
        $this->form = str_replace('<#' . $varName . '>', $var, $this->form);
    }

    /**
     *
     * @param type $items
     * @param type $contentDescriptiveOutput - will show the Div sections below the table with more information.
     * @return string - formated table of index
     * Renders a fully functional index table.
     */
    public function RenderIndexTable($items, $contentDescriptiveOutput = true) {

        $content = null;

        ob_start();

        echo '<ol>';

        foreach ($items as $key => $value) {

            if (is_array($value)) {

                $content .= '<div class="tableHeadingSection" id="' . $key . '">' . $key . '</div>';

                $content .= $this->outputIndexItems($key, $value);

            } else {
                
                if (is_numeric($key))
                    echo '<li class="indexHeading"><a href="#' . $value . '">' . $value . '</a></li>';
                else {
                    echo '<li class="indexHeading"><a href="#' . $key . '">' . $key . '</a></li>';

                    $content .= '<div class="widget"><div id="' . $key . '" class="indexTableContentHeading">' . $key . '</div>
                        <p>' . $this->formatCode($value) . '</p>
                        </div>
                    ';
                }
            }
        }

        echo '</ol><hr>';

        if ($contentDescriptiveOutput)
            echo $content;

        return ob_get_clean();
    }

    /**
     *
     * @param type $keys
     * @param type $items
     * @return string - creates headings within the menu for the RenderTableIndex method
     */
    private function outputIndexItems($keys, $items) {

        $content = null;

        echo '<li class="indexHeading"><a href="#' . $keys . '">' . $keys . '</a><ul>';

        foreach ($items as $key => $item) {

            if (is_array($item)) {

                $content .= '<div class="tableHeadingSection" id="' . $key . '">' . $key . '</div>';

                $this->outputIndexItems($key, $item);
            } else {
                if (is_numeric($key))
                    echo '<li><a href="#' . $item . '">' . $item . '</a></li>';
                else {
                    echo '<li><a href="#' . $key . '">' . $key . '</a></li>';

                    $content .= '<div class="widget"><div id="' . $key . '" class="indexTableContentHeading">' . $key . '</div>
                        <p>' . $this->formatCode($item) . '</p>
                        </div>
                    ';
                }
            }
        }

        echo '</ul></li>';

        return $content . '<hr>';
    }

    /**
     *
     * @param type $index
     * @return string - format code for displaying on html page.
     */
    public function formatCodeToHTML($index) {

        return $this->Variable($index)->Replace([

            '<php' => '&#60;php',
            '<' => '&#60;',
            '&#60;br />' => '<br />',
            '&#60;p>' => '<p>',
            '&#60;/p>' => '</p>',
            '&#60;/ol>' => '</ol>',
            '&#60;/li>' => '</li>',
            '&#60;li>' => '<li>',
            '&#60;ol>' => '<ol>',
            '&#60;div' => '<div',
            '&#60;/div' => '</div',

            ])->GetVariableResult();

//        $index = str_replace('<php', '&#60;php', $index);
//        $index = str_replace('<', '&#60;', $index);
//        $index = str_replace('&#60;br />', '<br />', $index);
//        $index = str_replace('&#60;p>', '<p>', $index);
//        $index = str_replace('&#60;/p>', '</p>', $index);
//        $index = str_replace('&#60;/ol>', '</ol>', $index);
//        $index = str_replace('&#60;/li>', '</li>', $index);
//        $index = str_replace('&#60;li', '<li', $index);
//        $index = str_replace('&#60;ol', '<ol', $index);
//        $index = str_replace('&#60;div', '<div', $index);
//        $index = str_replace('&#60;/div', '</div', $index);

//        return $index;
    }

    /**
     *
     * @param type $code
     * @return string - format code all together, code for html and colors.
     */
    public function formatCode($code) {

        $code = $this->formatCodeToHTML($code);
        $code = $this->formatCodeColors($code);

        return $code;
    }

    /**
     *
     * @param type $code
     * @return string - format html code colors.
     */
    public function formatCodeColors($code) {

        $replace = array(
            '&#60;php' => '<span class="red">&#60;php</span>',
            'if(' => '<span class="blue"> if ( </span>',
            '->' => '<span class="green"> -> </span>',
            '(' => '<span class="orange"> ( </span>',
            ')' => '<span class="orange"> )</span>',
            ' new ' => '<span class="lightgreen"> new </span>',
            ';' => '<span class="yellow"> ;</span>',
            '$this' => '<span class="this lightblue"> $this</span>',
            '$' => '<span class="brown"> $</span>',
            '::' => '<span class="blue"> <b>::</b> </span>',
            'Configs/' => '<b>Configs/</b>'
        );

        $code = $this->replaceMultiple($code, $replace);

        return $code;
    }

    /**
     *
     * @param type $haystack
     * @param type $needlesArray
     * @return string - Run a multiple search and replace function.
     */
    public function replaceMultiple($haystack, $needlesArray) {

        foreach ($needlesArray as $key => $value) {

            $haystack = str_replace($key, $value, $haystack);
        }

        return $haystack;
    }

    public function ToUpperCase($string) {

        $string = strtoupper($string);

        return $string;
    }

    public function ToLowerCase($string) {

        $string = strtolower($string);

        return $string;
    }

    public function FirstCharacterToUpperCase($string) {

        $string = strtoupper(substr($string, 0, 1)) . substr($string, 1);

        return $string;
    }

    public function FirstCharacterToLowerCase($string) {

        $string = strtolower(substr($string, 0, 1)) . substr($string, 1);

        return $string;
    }

    public function SpaceToBreak($string) {

        $string = str_replace(' ', '<br />', $string);

        return $string;
    }

    public function RenderRows($array) {

        $index = 1;

        $rows = null;

        if (count($array['tbody']) != 0) {

            $rows = '<tbody>';

            foreach ($array['tbody'] as $arr) {

                if ($index % 2 == 0)
                    $rows .= '<tr id="record_' . $index . '" class="even">';
                else
                    $rows .= '<tr id="record_' . $index . '" class="odd">';

                if (is_array($arr) || is_object($arr)) {

                    foreach ($arr as $key => $item) {

                        if (is_array($array['ignoreFields']) && count($array['ignoreFields']) != 0) {

                            foreach ($array['ignoreFields'] as $ignore) {

                                if ($key != $ignore)
                                    $rows .= '<td>' . $item . '</td>';
                            }
                        }
                        else
                            $rows .= '<td>' . $item . '</td>';
                    }

                    if (isset($array['actions'])) {

                        $rows .= '<td class="actions">

                        <div class="settings"></div>

                                <div class="settingsMenu">';

                        foreach ($array['actions'] as $key => $action) {

                            if (isset($action['message'])) {

                                $rows .= '<input type="hidden" value="' . @(isset($action['route']) ? $this->setRoute($action['route'], array($action['routeParam'] => (is_object($arr) ? $arr->$action['dataParam'] : $arr[$action['dataParam']] ))) : $action['url'] ) . '">';

                                $rows .= ' <span class="confirmAction ' . @$action['class'] . '" id="' . $key . '_' . $index . '">' . $key . '</span> ';

                                $rows .= '<input type="hidden" value="' . $action['message'] . '">';
                            } else {

                                $rows .= ' <a id="' . $key . '_' . $index . '" target="' . @$action['target'] . '" class="' . @$action['class'] . '" href="' . @(isset($action['route']) ? $this->setRoute($action['route'], array($action['routeParam'] => (is_object($arr) ? $arr->$action['dataParam'] : $arr[$action['dataParam']] ))) : @$action['url'] ) . '">' . $key . '</a> ';
                            }

                            $rows .= '<br />';
                        }

                        $rows .= '</div></td>';
                    }
                }
                else
                    $rows .= '<td>' . $arr . '</td>';

                $rows .= '</tr>';

                $index++;
            }

            return $rows . '</tbody>';
        }
        else
            return '<td>No Record(s) Found</td>';
    }

    public function RenderTable($array) {

        $rows = function() use ($array) {

            $rows .= $this->renderTableHead($array);

            $rows .= $this->RenderRows($array);

            $rows .= $this->renderTableFooter($array);

            return $rows;
        };

        return $rows();
    }

    private function renderTableFooter($array) {

        $rows = function($row = null) use ($array) {

            $row .= '<tfoot><tr>';

            if (isset($array['tfoot']))
                foreach ($array['tfoot'] as $arr) {

                    $row .= '<td>' . $arr . '</td>';
                }

            $row .= '</tr></tfoot></table>';

            return $row;
        };

        return $rows();
    }

    public function renderTableHead($array) {

        $heading = function ($rows = null) use ($array) {

            if (isset($array['title']))
                $rows = '<div class="title"><h6>' . $array['title'] . '</h6></div>';

            $rows .= '<table class="' . @$array['class'] . '" id="' . @$array['id'] . '"><thead><tr>';

            return $rows;
        };

        $tableTitles = function ($rows = null) use ($array) {

            if (isset($array['thead']) && $this->isLoopable($array['thead'])) {

            foreach ($array['thead'] as $arr) {

                $rows .= '<th>' . $arr . '</th>';
            }

                $rows .= '</tr></thead>';
            } else {

                foreach ($array['tbody'] as $arr) {

                    foreach ($arr as $key => $val) {

                        if (is_array($array['ignoreFields']) && count($array['ignoreFields']) != 0) {

                            foreach ($array['ignoreFields'] as $ignore) {

                                if ($key != $ignore)
                                    $rows .= '<th>' . str_replace('_', ' ', $this->FirstCharacterToUpperCase($key)) . '</th>';
                            }
                        }
                        else
                            $rows .= '<th>' . str_replace('_', ' ', $this->FirstCharacterToUpperCase($key)) . '</th>';
                    }

                    break;
                }
            }

            return $rows;

        };

        $tableActions = function($rows = null) use ($array) {

            if (isset($array['actions'])) {

                $rows .= '<th>Actions</th>';
            }

            $rows .= '</tr></thead>';

            return $rows;
        };

        return $heading() . $tableTitles() . $tableActions();
    }

    /**
     *
     * @param array $sectionData
     * @return string
     * Renders sections html, can be set to two types:
     * Pages
     * Accordians
     */
    public function RenderSections(array $sectionData) {

        $sectionHeader = function($title, $section, $index, $sections = null) use ($sectionData){

            if (strtolower($sectionData['type']) == 'accordian') $sections .= '<div class="title"><h6>' . $title . '</h6></div>';

            $sections .= '<div class="section ' . @$section['class'] . '" id="section' . $index . '">';

            $sections .= '<div class="sectionHeader">';
            $sections .= $section['header'];
            $sections .= '</div>';

            return $sections;

        };

        $sectionBody = function($section, $sections = null){

            $sections .= '<div class="sectionBody">';
            $sections .= $section['body'];
            $sections .= '</div>';

            return $sections;
        };

        $sectionFooter = function ($section, $sections = null){

            $sections .= '<div class="sectionFooter">';
            $sections .= $section['footer'];
            $sections .= '</div>';

            $sections .= '</div>';

            return $sections;
        };

        if (is_array($sectionData)) {

            $sections = '<div class="Sections">';

            $index = 1;

            if (!isset($sectionData['type']))
                $sectionData['type'] = 'pages';

            if (strtolower($sectionData['type']) == 'pages')
                $sections .= '<div class="title"><h6>' . $sectionData['title'] . '</h6></div>';

            foreach ($sectionData['sections'] as $title => $section) {

                $sections .= $sectionHeader($title, $section, $index) . $sectionBody($section) . $sectionFooter();

                $index += 1;
            }

            $sectionStats = function(){

                return '<div class="SectionsFooter"><div class="SectionStats">Page: <span id="Section">1</span></div><div class="SectionsButtons"><input type="button" value="Previous" class="prev"><input type="button" value="Next" class="next"></div></div>';

            };

            $sections .= $sectionStats() . '</div>';

            return $sections;
        }
        else
            echo 'INVALID DATA FOR RENDERING SECTIONS';
    }

    /**
     * @param Mixed $string Can be an array or string depending on the filter applied
     * @param string $filter Options given below
     *
     * You can use the output method to filter your output character cases.<br /><br />
     * Four filters can be applied:<br /><br />
     * upper - subject: string<br />
     * lower - subject: string<br />
     * firstUpper or charUpper - subject: string<br />
     * firstLower or charLower - subject: string<br />
     * spacetobreak - subject: string<br />
     * list/orderedlist - subject: array<br />
     * unorderelist - subject: array<br />
     * tablebody/tablerows - subject: array<br />
     * table - subject: array<br />
     * tablehead - subject: array<br />
     * form - subject: array<br />
     * indextable - subject: array<br />
     * sections - subject: array <br />
     *
     */
    public function Output($subject, $filter = null) {

        $filter = strtolower(str_replace(' ', '', $filter));

        if (!empty($filter))
            switch ($filter) {

                case 'upper':
                    $subject = $this->ToUpperCase($subject);
                    break;
                case 'lower':
                    $subject = $this->ToLowerCase($subject);
                    break;
                case 'charupper':
                case 'firstupper':
                    $subject = $this->FirstCharacterToUpperCase($subject);
                    break;
                case 'charlower':
                case 'firstlower':
                    $subject = $this->FirstCharacterToLowerCase($subject);
                    break;
                case 'spacetobreak':
                    $subject = $this->SpaceToBreak($subject);
                    break;
                case 'list':
                case 'orderedlist':
                    $subject = '<ol><li>' . str_replace(' ', '</li><li>', $subject) . '</li></ol>';
                    break;
                case 'unorderedlist':
                    $subject = '<ul><li>' . str_replace(' ', '</li><li>', $subject) . '</li></ul>';
                    break;
                case 'tablebody':
                case 'tablerows':
                    $html = new HTMLGenerator();
                    $subject = $html->renderRows($subject);
                    break;
                case 'table':
                    $html = new HTMLGenerator();
                    $subject = $html->renderTable($subject);
                    break;
                case 'tablehead':
                    $html = new HTMLGenerator();
                    $subject = $html->renderTableHead($subject);
                    break;
                case 'form':
                    $html = new HTMLGenerator();
                    $subject = $html->generateForm($subject);
                    break;
                case 'indextable':
                    $html = new HTMLGenerator();
                    $subject = $html->RenderIndexTable($subject);
                    break;
                case 'sections':
                    $html = new HTMLGenerator();
                    $subject = $html->RenderSections($subject);
                    break;
                default:
                    echo 'invalid filter';
                    break;
            }

        return $subject;
    }

    public function element($element = array()) {

        $this->element = $element;

        return $this;
    }

    public function style($style = array()) {

        $this->style = $style;

        return $this;
    }

    public function addClass($class) {

        $this->class = $class;

        return $this;
    }

    public function id($id) {

        $this->id = $id;

        return $this;
    }

    public function renderElement() {

        $element = $this->element;
        $element['style'] = $this->style;
        $element['id'] = $this->id;
        $element['class'] = $this->class;

        $renderedElement = $this->add($element);

        return $renderedElement;
    }

}