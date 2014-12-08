<?php

use Router\Router;

Router::Add('Welcome', array(

      "Controller" => "Welcome:Welcome:index",
      "Pattern" => "/Welcome/"

));

Router::Add('Welcome_List', array(

      "Controller" => "Welcome:Welcome:list",
      "Pattern" => "/Welcome/List/"

));

Router::Add('Welcome_View', array(

      "Controller" => "Welcome:Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

));

Router::Add('Welcome_Create', array(

      "Controller" => "Welcome:Welcome:create",
      "Pattern" => "/Welcome/Create/"

));

Router::Add('Welcome_Edit', array(

      "Controller" => "Welcome:Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

));

Router::Add('Welcome_Delete', array(

      "Controller" => "Welcome:Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

));
