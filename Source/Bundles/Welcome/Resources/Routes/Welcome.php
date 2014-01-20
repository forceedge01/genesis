<?php


Set::Route('Welcome', array(

      "Controller" => "Welcome:Welcome:index",
      "Pattern" => "/Welcome/"

));

Set::Route('Welcome_List', array(

      "Controller" => "Welcome:Welcome:list",
      "Pattern" => "/Welcome/List/"

));

Set::Route('Welcome_View', array(

      "Controller" => "Welcome:Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

));

Set::Route('Welcome_Create', array(

      "Controller" => "Welcome:Welcome:create",
      "Pattern" => "/Welcome/Create/"

));

Set::Route('Welcome_Edit', array(

      "Controller" => "Welcome:Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

));

Set::Route('Welcome_Delete', array(

      "Controller" => "Welcome:Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

));
