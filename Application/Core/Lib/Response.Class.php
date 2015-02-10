<?php

namespace Application\Core;



use Application\Core\Interfaces\Response as ResponseInterface;

class Response implements ResponseInterface{

    // Message for the error being set
    private $messages = array();
    // Exception set
    private $exception;
    // Data to be returned
    private $returnData = array();
    // Set the service result
    private $result;
    // Set the success of the current method
    private $success;
    // Set content of the response
    private $content;

    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setCookie($name, $value , $time = 2592000){

        setcookie($name, '', -(time() + 2592000));

        if(setcookie($name, $value, time() + $time, '/'))
             return $this;
        else
            return false;
    }

    public function removeCookie($Name)
    {
        if(setcookie($Name, '', -(time() + 2592000)))
            return $this;
        else
            return false;
    }

    public function setSession($Name, $Value){

        $_SESSION[$Name] = $Value;

        return $this;
    }

    public function unsetSession($Name){

        unset($_SESSION[$Name]);

        return $this;
    }

    /**
     *
     * @param string $message
     * @param string $type
     * @return \Bundles\CoreBundle\Entity\Error
     */
    public function addFlashMessage($message, $type = 'danger') {
        $allowedTypes = array(
            'danger',
            'info',
            'success',
            'warning'
        );

        if(array_search($type, $allowedTypes) === false) {
            throw new \Exception("Invalid Flash message type provided: $type");
        }

        $this->messages[$type][] = $message;

        return $this;
    }

    /**
     *
     * @param string $message
     * @param int $code
     */
    public function setException($message, $code) {
        $this->exception = new \Exception($message, $code);
    }

    public function throwException() {
        throw $this->exception;
    }

    /**
     *
     * @return array | 0
     */
    public function hasFlashMessages() {
        return $this->messages || [];
    }

    /**
     *
     * @return \Exception
     */
    public function hasException() {
        return $this->exception || 0;
    }

    /**
     *
     * @return array
     */
    public function getFlashMessages() {
        return $this->messages;
    }

    /**
     *
     * @param type $result
     * @return \Bundles\CoreBundle\Entity\FandistResponse
     */
    public function setResult($result) {
        $this->result = $result;

        return $this;
    }

    /**
     *
     * @return type
     */
    public function getResult() {
        return $this->result;
    }

    public function hasResult() {
        return $this->result || 0;
    }

    public function setSuccess($message = null, $type = 'success') {
        if($message) {
            $this->addFlashMessage($message, $type);
        }        
        $this->success = 1;

        return $this;
    }

    public function setFailure($message = null, $type = 'danger') {
        if($message) {
            $this->addFlashMessage($message, $type);
        }
        $this->success = 0;

        return $this;
    }

    public function hasFailed() {
        return $this->success === 0;
    }

    public function isSuccessful() {
        return $this->success === 1;
    }

    public function setStatus($code)
    {
        header( 'HTTP/1.1 '.$code, true, $code );
    }

    public function setNotFound()
    {
        $this ->setStatus(404);
    }

    public function setBadRequest()
    {
        $this ->setStatus(400);
    }

    public function setInternalServerError()
    {
        $this ->setStatus(500);
    }

    public function setForbidden()
    {
        $this ->setStatus(403);
    }

    public function setJSONResponse($json)
    {
        return $this->content = json_encode($value);
    }
}
