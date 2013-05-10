<?php

namespace Application\Interfaces;



interface Getter{
    
    public function GetComponent( );
    
    public function GetObject( );
    
    public function GetCoreObject( );
    
    public function GetRequestManager( );
    
    public function GetEntity( );
    
    public function GetRepository( );
    
    public function GetDatabaseManager ( );
    
    public function GetSessionManager ( );
    
    public function GetRouterManager ( );
}