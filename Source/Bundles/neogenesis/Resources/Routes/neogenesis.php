<?php


Set::Route('neogenesis', array(

      "Controller" => "neogenesis:neogenesis:index",
      "Pattern" => "/neogenesis/"
));


Set::Route('neogenesis_List', array(

      "Controller" => "neogenesis:neogenesis:list",
      "Pattern" => "/neogenesis/List/"
));


Set::Route('neogenesis_View', array(

      "Controller" => "neogenesis:neogenesis:view",
      "Pattern" => "/neogenesis/View/{id}/"
));


Set::Route('neogenesis_Create', array(

      "Controller" => "neogenesis:neogenesis:create",
      "Pattern" => "/neogenesis/Create/"
));


Set::Route('neogenesis_Edit', array(

      "Controller" => "neogenesis:neogenesis:edit",
      "Pattern" => "/neogenesis/Edit/{id}/"
));


Set::Route('neogenesis_Delete', array(

      "Controller" => "neogenesis:neogenesis:delete",
      "Pattern" => "/neogenesis/Delete/{id}/"
));