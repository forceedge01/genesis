<?php


Set::Route('people', array(

      "Controller" => "people:people:index",
      "Pattern" => "/people/"
));


Set::Route('people_List', array(

      "Controller" => "people:people:list",
      "Pattern" => "/people/List/"
));


Set::Route('people_View', array(

      "Controller" => "people:people:view",
      "Pattern" => "/people/View/{id}/"
));


Set::Route('people_Create', array(

      "Controller" => "people:people:create",
      "Pattern" => "/people/Create/"
));


Set::Route('people_Edit', array(

      "Controller" => "people:people:edit",
      "Pattern" => "/people/Edit/{id}/"
));


Set::Route('people_Delete', array(

      "Controller" => "people:people:delete",
      "Pattern" => "/people/Delete/{id}/"
));