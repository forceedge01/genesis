<?php

use \Application\Core\Router;



Router::$Route['testing'] = array(

      "Controller" => "testing:testing:index",
      "Pattern" => "/testing/"
);

Router::$Route['testing_List'] = array(

      "Controller" => "testing:testing:list",
      "Pattern" => "/testing/List/"
);

Router::$Route['testing_View'] = array(

      "Controller" => "testing:testing:view",
      "Pattern" => "/testing/View/{id}/"
);

Router::$Route['testing_Create'] = array(

      "Controller" => "testing:testing:create",
      "Pattern" => "/testing/Create/"
);

Router::$Route['testing_Edit'] = array(

      "Controller" => "testing:testing:edit",
      "Pattern" => "/testing/Edit/{id}/"
);

Router::$Route['testing_Delete'] = array(

      "Controller" => "testing:testing:delete",
      "Pattern" => "/testing/Delete/{id}/"
);
              