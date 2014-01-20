<?php

namespace Application\Components;

/**
 * Author: Wahab Qureshi.
 */

class DatabaseHtmlGenerator extends HTMLGenerator {
    
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
}