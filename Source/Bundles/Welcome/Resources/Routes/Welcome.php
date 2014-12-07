<?php

use Router\Router;

Router::Set('Welcome', array(

      "Controller" => "Welcome:Welcome:index",
      "Pattern" => "/Welcome/"

));

Router::Set('Welcome_List', array(

      "Controller" => "Welcome:Welcome:list",
      "Pattern" => "/Welcome/List/"

));

Router::Set('Welcome_View', array(

      "Controller" => "Welcome:Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

));

Router::Set('Welcome_Create', array(

      "Controller" => "Welcome:Welcome:create",
      "Pattern" => "/Welcome/Create/"

));

Router::Set('Welcome_Edit', array(

      "Controller" => "Welcome:Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

));

Router::Set('Welcome_Delete', array(

      "Controller" => "Welcome:Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

));
