<?php

Router::$Route['testBundle'] = array(

      "Controller" => "testBundle:index",
      "Pattern" => "/testBundle/"
);

Router::$Route['testBundle_List'] = array(

      "Controller" => "testBundle:list",
      "Pattern" => "/testBundle/List/"
);

Router::$Route['testBundle_View'] = array(

      "Controller" => "testBundle:view",
      "Pattern" => "/testBundle/View/{id}/"
);

Router::$Route['testBundle_Create'] = array(

      "Controller" => "testBundle:create",
      "Pattern" => "/testBundle/Create/"
);

Router::$Route['testBundle_Edit'] = array(

      "Controller" => "testBundle:edit",
      "Pattern" => "/testBundle/Edit/{id}/"
);

Router::$Route['testBundle_Delete'] = array(

      "Controller" => "testBundle:delete",
      "Pattern" => "/testBundle/Delete/{id}/"
);
