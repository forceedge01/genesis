<?php

namespace Bundles\users\Models;



use Bundles\users\Interfaces\usersModelinterface;

// Model represents the logic of users table with the application

final class usersModel implements usersModelinterface {

    public function __construct(\Bundles\users\Entities\usersEntity $usersEntity) {
        ;
    }
}