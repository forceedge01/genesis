<?php

namespace Application\Core;



class Response{
    
    public function SetStatus($code)
    {
        header( 'HTTP/1.1 '.$code, true, $code );
    }
    
    public function SetNotFound()
    {
        $this ->SetStatus(404);
    }
    
    public function SetBadRequest()
    {
        $this ->SetStatus(400);
    }
    
    public function SetInternalServerError()
    {
        $this ->SetStatus(500);
    }
    
    public function SetForbidden()
    {
        $this ->SetStatus(403);
    }
    
    public function GetStatus()
    {
        return $_SERVER["REDIRECT_STATUS"];
    }
    
    public function JsonResponse($value)
    {
        return json_encode($value);
    }
}