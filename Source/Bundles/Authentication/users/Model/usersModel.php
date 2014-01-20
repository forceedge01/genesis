<?php

namespace Bundles\Authentication\users\Models;

use Bundles\users\Interfaces\usersModelinterface;
use Application\Models\ApplicationModel;

// Model represents the logic of users table with the application

final class usersModel extends ApplicationModel implements usersModelinterface {

    public function CreateUser()
    {
        $this->GetEntityObject()->users__password = hash(\Get::Config('Auth.Security.PasswordEncryption'), $this->entityObject->users__password);

        if ($this->GetEntityObject()->Save($this->entityObject))
            return true;

        return false;
    }

    public function UpdateUser()
    {
        $this->GetEntityObject()->users__password = hash(\Get::Config('Auth.Security.PasswordEncryption'), $this->entityObject->users__password);

        if ($this->GetEntityObject()->Save())
            return true;

        return false;
    }

    public function DeleteUser()
    {
        if ($this->GetEntityObject()->Delete())
            return true;

        return false;
    }
}