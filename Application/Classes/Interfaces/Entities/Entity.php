<?php

namespace Application\Interfaces\Entities;



interface Entity{

    function Get($id = null);

    function Save(array $params = array());

    function Delete($id = null);
}