<?php

use \Application\Core\Router;



Router::$Route['people'] = array(

      "Controller" => "people:people:index",
      "Pattern" => "/people/"
);

Router::$Route['people_List'] = array(

      "Controller" => "people:people:list",
      "Pattern" => "/people/List/"
);

Router::$Route['people_View'] = array(

      "Controller" => "people:people:view",
      "Pattern" => "/people/View/{id}/"
);

Router::$Route['people_Create'] = array(

      "Controller" => "people:people:create",
      "Pattern" => "/people/Create/"
);

Router::$Route['people_Edit'] = array(

      "Controller" => "people:people:edit",
      "Pattern" => "/people/Edit/{id}/"
);

Router::$Route['people_Delete'] = array(

      "Controller" => "people:people:delete",
      "Pattern" => "/people/Delete/{id}/"
);
              