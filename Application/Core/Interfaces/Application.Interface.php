<?php

namespace Application\Core\Interfaces;



interface Application{

    function __destruct();

    function checkIfAccessableBy($roleId);

    function userRoleIs($user);
}
