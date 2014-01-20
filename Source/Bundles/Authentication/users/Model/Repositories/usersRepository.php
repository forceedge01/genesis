<?php

namespace Bundles\Authentication\users\Repositories;



use \Application\Repositories\ApplicationRepository;

use \Bundles\users\Interfaces\usersRepositoryInterface;

// This Repository holds methods to query users table

final class usersRepository extends ApplicationRepository implements usersRepositoryInterface{

    public function populateUser()
    {
        return $this->Table('users', array('id', 'email'))->GetOneRecordBy(array('email' => $this->GetCoreObject('Session')->Get('username')));
    }
}
