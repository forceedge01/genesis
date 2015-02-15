<?php

namespace Application\Core\Interfaces;



interface ResponseInterface{

    public function setStatus($code);

    public function getContent();

    public function setContent($content);

    public function handle();

    public function addFlashMessage($message, $type = 'danger');

    public function getFlashMessages();

    public function setError();

    public function hasError();

    public function hasFailed();

    public function isSuccessful();

    public function setFailure();

    public function setSuccess();

    public function setResult($result);

    public function hasResult();

    public function getResult();

    public function setException($message, $code);

    public function hasException();

    public function throwException();
}
