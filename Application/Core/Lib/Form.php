<?php

namespace Application\Core\Lib;



Class Form {
	
	public function Get($name) {
        return filter_has_var(INPUT_REQUEST, $name) ? filter_input(INPUT_REQUEST, $name) : false;
    }

    public function getAllData() {
        return $_REQUEST;
    }

    public function has($name) {
    	return filter_has_var(INPUT_REQUEST, $name) ?: false;
    }
}
