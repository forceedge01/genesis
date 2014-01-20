<?php


Set::Route('Clients', array(

    'Controller' => 'Clients:Clients:index',
    'Pattern' => '/Clients/'
));

Set::Route('Clients_List', array(

    'Controller' => 'Clients:Clients:list',
    'Pattern' => '/Clients/List/'
));

Set::Route('Clients_View', array(

    'Controller' => 'Clients:Clients:view',
    'Pattern' => '/Clients/View/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));

Set::Route('Clients_Create', array(

    'Controller' => 'Clients:Clients:create',
    'Pattern' => '/Clients/Create/'
));

Set::Route('Clients_Edit', array(

    'Controller' => 'Clients:Clients:edit',
    'Pattern' => '/Clients/Edit/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));

Set::Route('Clients_Delete', array(

    'Controller' => 'Clients:Clients:delete',
    'Pattern' => '/Clients/Delete/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));
