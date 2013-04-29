<?php

use \Application\Core\Router;



Router::$Route['Welcome'] = array(

      "Controller" => "Welcome:Welcome:index",
      "Pattern" => "/Welcome/"

);

Router::$Route['Welcome_List'] = array(

      "Controller" => "Welcome:Welcome:list",
      "Pattern" => "/Welcome/List/"

);

Router::$Route['Welcome_View'] = array(

      "Controller" => "Welcome:Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

);

Router::$Route['Welcome_Create'] = array(

      "Controller" => "Welcome:Welcome:create",
      "Pattern" => "/Welcome/Create/"

);

Router::$Route['Welcome_Edit'] = array(

      "Controller" => "Welcome:Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

);

Router::$Route['Welcome_Delete'] = array(

      "Controller" => "Welcome:Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

);
