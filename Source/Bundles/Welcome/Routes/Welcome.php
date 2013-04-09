<?php

$_SESSION['Routes']['Welcome'] = array(

      "Controller" => "Welcome:index",
      "Pattern" => "/Welcome/"

);

$_SESSION['Routes']['Welcome_List'] = array(

      "Controller" => "Welcome:list",
      "Pattern" => "/Welcome/List/"

);

$_SESSION['Routes']['Welcome_View'] = array(

      "Controller" => "Welcome:view",
      "Pattern" => "/Welcome/View/{id}/"

);

$_SESSION['Routes']['Welcome_Create'] = array(

      "Controller" => "Welcome:create",
      "Pattern" => "/Welcome/Create/"

);

$_SESSION['Routes']['Welcome_Edit'] = array(

      "Controller" => "Welcome:edit",
      "Pattern" => "/Welcome/Edit/{id}/"

);

$_SESSION['Routes']['Welcome_Delete'] = array(

      "Controller" => "Welcome:delete",
      "Pattern" => "/Welcome/Delete/{id}/"

);
