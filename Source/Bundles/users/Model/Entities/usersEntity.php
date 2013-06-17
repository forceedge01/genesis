<?php

namespace Application\Bundles\users\Entities;



use \Application\Core\Entities\ApplicationEntity;

// This Entity represents users table

final class usersEntity extends ApplicationEntity {

    public function populateUser()
    {
        return $this->Table('users', array('id', 'email'))->GetOneRecordBy(array('email' => $this->GetCoreObject('Session')->Get('username')));
    }
}
