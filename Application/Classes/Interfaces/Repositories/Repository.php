<?php

namespace Application\Interfaces\Repositories;



interface Repository{

    function find($id);

    function findOneBy(array $params);

    function findAll(array $params);

    function GetAll(array $params);

    function GetCount($column);
}