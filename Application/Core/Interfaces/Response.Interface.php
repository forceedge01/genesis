<?php

namespace Application\Core\Interfaces;



interface Response{

    public function setStatus($code);

    public function setJSONResponse($value);

    public function getContent();

    public function setContent($content);
}
