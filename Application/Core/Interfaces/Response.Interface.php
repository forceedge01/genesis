<?php

namespace Application\Core\Interfaces;



interface Response{

    public function SetStatus($code);

    public function SetNotFound();

    public function SetBadRequest();

    public function SetInternalServerError();

    public function SetForbidden();

    public function GetStatus();

    public function JsonResponse($value);
}