<?php

namespace Application\Core\Interfaces;



interface ObjectManager{

    public function GetComponent( $object, $args );

    // public function GetObject($object, $args );

    public function GetCoreObject( $object, $args );

    public function GetRequestManager( );

    public function GetEntity($bundleColonEntityName );

    public function GetRepository( $bundleColonEntityName);

    public function GetDatabaseManager ( );

    public function GetSessionManager ( );

    public function GetRouterManager ( );
}