<?php

namespace Application\Interfaces\Entities;



interface Entity{

    function Find($id = null);

    function Save($params = array());

    function Delete($id = null);
}