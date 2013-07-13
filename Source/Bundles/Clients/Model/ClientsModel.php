<?php

namespace Bundles\Clients\Models;



use \Bundles\Clients\Interfaces\ClientsModelInterface;

// Model represents the logic of Clients table with the application

final class ClientsModel implements ClientsModelInterface{

    public function CreateClient()
    {
        if ($this->GetEntityObject()->Save($this->entityObject))
            return true;

        return false;
    }

    public function UpdateClient()
    {
        if ($this->GetEntityObject()->Save())
            return true;

        return false;
    }

    public function DeleteClient()
    {
        if ($this->GetEntityObject()->Delete())
            return true;

        return false;
    }
}