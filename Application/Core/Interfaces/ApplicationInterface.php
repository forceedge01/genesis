<?php

namespace Application\Core\Interfaces;



interface ApplicationInterface{

    function __destruct();

    function checkIfAccessableBy($roleId);

    function userRoleIs($user);
}
