<?php

Router::$Route['facebook'] = array(

      "Controller" => "facebook:index",
      "Pattern" => "/facebook/"
);

Router::$Route['facebook_List'] = array(

      "Controller" => "facebook:list",
      "Pattern" => "/facebook/List/"
);

Router::$Route['facebook_View'] = array(

      "Controller" => "facebook:view",
      "Pattern" => "/facebook/View/{id}/"
);

Router::$Route['facebook_Create'] = array(

      "Controller" => "facebook:create",
      "Pattern" => "/facebook/Create/"
);

Router::$Route['facebook_Edit'] = array(

      "Controller" => "facebook:edit",
      "Pattern" => "/facebook/Edit/{id}/"
);

Router::$Route['facebook_Delete'] = array(

      "Controller" => "facebook:delete",
      "Pattern" => "/facebook/Delete/{id}/"
);
              