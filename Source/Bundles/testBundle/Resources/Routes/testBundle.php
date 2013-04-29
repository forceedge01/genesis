<?php

use \Application\Core\Router;



Router::$Route['testBundle'] = array(

      "Controller" => "testBundle:testBundle:index",
      "Pattern" => "/testBundle/"
);

Router::$Route['testBundle_List'] = array(

      "Controller" => "testBundle:testBundle:list",
      "Pattern" => "/testBundle/List/"
);

Router::$Route['testBundle_View'] = array(

      "Controller" => "testBundle:testBundle:view",
      "Pattern" => "/testBundle/View/{id}/"
);

Router::$Route['testBundle_Create'] = array(

      "Controller" => "testBundle:testBundle:create",
      "Pattern" => "/testBundle/Create/"
);

Router::$Route['testBundle_Edit'] = array(

      "Controller" => "testBundle:testBundle:edit",
      "Pattern" => "/testBundle/Edit/{id}/"
);

Router::$Route['testBundle_Delete'] = array(

      "Controller" => "testBundle:testBundle:delete",
      "Pattern" => "/testBundle/Delete/{id}/"
);
