<?php

Router::$Route['Welcome'] = array(

      "Controller" => "Welcome:index",
      "Pattern" => "/Welcome/"

);

Router::$Route['Welcome_List'] = array(

      "Controller" => "Welcome:list",
      "Pattern" => "/Welcome/List/"

);

Router::$Route['Welcome_View'] = array(

      "Controller" => "Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

);

Router::$Route['Welcome_Create'] = array(

      "Controller" => "Welcome:create",
      "Pattern" => "/Welcome/Create/"

);

Router::$Route['Welcome_Edit'] = array(

      "Controller" => "Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

);

Router::$Route['Welcome_Delete'] = array(

      "Controller" => "Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

);
