<?php

use \Application\Core\Router;



Router::$Route['neogenesis'] = array(

      "Controller" => "neogenesis:neogenesis:index",
      "Pattern" => "/neogenesis/"
);

Router::$Route['neogenesis_List'] = array(

      "Controller" => "neogenesis:neogenesis:list",
      "Pattern" => "/neogenesis/List/"
);

Router::$Route['neogenesis_View'] = array(

      "Controller" => "neogenesis:neogenesis:view",
      "Pattern" => "/neogenesis/View/{id}/"
);

Router::$Route['neogenesis_Create'] = array(

      "Controller" => "neogenesis:neogenesis:create",
      "Pattern" => "/neogenesis/Create/"
);

Router::$Route['neogenesis_Edit'] = array(

      "Controller" => "neogenesis:neogenesis:edit",
      "Pattern" => "/neogenesis/Edit/{id}/"
);

Router::$Route['neogenesis_Delete'] = array(

      "Controller" => "neogenesis:neogenesis:delete",
      "Pattern" => "/neogenesis/Delete/{id}/"
);
