<?php

$_SESSION['Routes']['testBundle'] = array(

      "Controller" => "testBundle:index",
      "Pattern" => "/testBundle/"

);

$_SESSION['Routes']['testBundle_List'] = array(

      "Controller" => "testBundle:list",
      "Pattern" => "/testBundle/List/"

);

$_SESSION['Routes']['testBundle_View'] = array(

      "Controller" => "testBundle:view",
      "Pattern" => "/testBundle/View/{id}/"

);

$_SESSION['Routes']['testBundle_Create'] = array(

      "Controller" => "testBundle:create",
      "Pattern" => "/testBundle/Create/"

);

$_SESSION['Routes']['testBundle_Edit'] = array(

      "Controller" => "testBundle:edit",
      "Pattern" => "/testBundle/Edit/{id}/"

);

$_SESSION['Routes']['testBundle_Delete'] = array(

      "Controller" => "testBundle:delete",
      "Pattern" => "/testBundle/Delete/{id}/"

);
