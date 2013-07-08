<?php

namespace Bundles\users\Entities;



use \Application\Entities\ApplicationEntity;

// This Entity represents users table

final class usersEntity extends ApplicationEntity {

    public
            $id,
            $email,
            $password,
            $createdAt;

    public function populateUser()
    {
        return $this->Table('users', array('id', 'email'))->GetOneRecordBy(array('email' => $this->GetCoreObject('Session')->Get('username')));
    }
}
