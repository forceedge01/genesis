<?php

class Bundle extends Console {

    public
            $name,
            $renderMethod;

    public function __construct($type) {

        $this->renderMethod = $type;
    }

    public function createBundle() {

        if(!isset($_SERVER['SERVER_NAME'])){

            echo 'Enter name of the bundle you want to create (If you are using a database with this application, this is usually the singular form of your table name): ';

            $this->name = str_replace('bundle', '', strtolower($this->readUser()));

        }

        if (mkdir(BUNDLES_FOLDER . $this->name)) {

            $this->createConfig()->createRoutes()->createController()->createEntity()->createViews()->CreateAssets();
        }

        echo 'Bundle ' . $this->name . ' has been created successfully!';

        $this->linebreak(2);
    }

    public function deleteBundle() {

        if(!isset($_SERVER['SERVER_NAME'])){

            $this->linebreak(1);

            echo 'Bundles you have in your application: ';

            $this->linebreak(1);

            $this->readBundles(false);

            $this->linebreak(1);

            echo 'Enter name of the bundle you want to delete: ';

            $bundleName = $this->readUser();
            $this->linebreak(1);

        }
        else
            $bundleName = $this->name;

        if(!empty($bundleName))
            if ($this->removeDirectory(BUNDLES_FOLDER . $bundleName))
                echo 'Bundle has been deleted successfully.';
            else
                echo 'Unable to delete bundle.';
        else
            echo 'Bundle must have a name!';
    }

    public function readBundles($return) {

        $bundles = scandir(BUNDLES_FOLDER);

        $bundlesArray = array();

        foreach ($bundles as $bundle) {

            if (is_dir(BUNDLES_FOLDER . $bundle)) {

                if($bundle != '.' && $bundle != '..') {

                    if($return)
                        $bundlesArray[] = $bundle;
                    else{

                        echo $bundle;
                        $this->linebreak(1);
                    }
                }
            }
        }

        return $bundlesArray;
    }

    private function createConfig(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Configs');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Configs/' . $this->name . '.php', 'w+');

        $initTemplate = '<?php

DEFINE(\'BUNDLE_'.strtoupper($this->name).'_PATH\', BUNDLES_FOLDER . \''.$this->name.'\');';

        fwrite($handle, $initTemplate);

        fclose($handle);

        return $this;
    }

    private function createEntity(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Entities');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/' . 'Entities/ ' . $this->name . 'Entity.php', 'w+');

        $initEntity = '<?php

class ' . $this->name . ' extends ApplicationEntity{

      protected
                  $id,
                  $tableColumns,
                  $joinQuery,
                  $tableName;

      public function __construct($id = null){

         parent::__construct();

         $this->tableColumns = array(\'*\');

         $this->tableName = __CLASS__;

         if(is_numeric($id)){

            $this->id = $id;
            $this->Get();

         }
      }

      /**
       *
       * @param Array $param Params can include where clause order by clause or any other mysql clause.
       * @return mixed Returns matching data set.
       */
      public function GetAll(array $params = array()){

        return $this->Table($this->tableName, $this->tableColumns)->GetRecords($params)->GetResultSet();

      }

      /**
       *
       * @param Mixed $id Can be the primary key value or an array of column and values
       * @return mixed Returns the matching data set from the database.
       */
      public function Get($id = null){

        if(!$id)
            $id = $this->id;

        return $this->Table($this->tableName, $this->tableColumns)->GetRecordBy($id)->GetFirstResult();

      }

      /**
       *
       * @param array $params Pass in the data for saving it to the database, if not provided<br>
       * the submitted data in globals will be taken and matched to the table on which the operation is applied.
       */
      public function Save(array $params = array()){

        return $this->Table($this->tableName)->SaveRecord($params)->GetAffectedRows();

      }

      /**
       *
       * @param int $id the id of the record to be deleted
       * @return int Number of rows affected
       */
      public function Delete($id = null){

        if(!$id)
            $id = $this->id;

        return $this->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();

      }
}
              ';

        fwrite($handle, $initEntity);

        fclose($handle);

        return $this;
    }

    private function createViews(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Views');

        mkdir(BUNDLES_FOLDER . $this->name . '/Views/ControllerViews');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/' . 'Header.html.php', 'w+');

        $initTemplate = '<?=$this->RenderTemplate("Templates::Header.html.php", $params)?>';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/' . 'Footer.html.php', 'w+');

        $initTemplate = ' <?=$this->RenderTemplate("Templates::Footer.html.php", $params)?>';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/ControllerViews/list.html.php', 'w+');

        $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_Create\')?>">Create new '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/ControllerViews/view.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/ControllerViews/create.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($form, \'form\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Views/ControllerViews/edit.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($form, \'form\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            return $this;
    }

    private function createController(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Controllers');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Controllers/' . $this->name . 'Controller.php', 'w+');

        $initController = '<?php

class ' . $this->name . 'Controller extends ' . $this->name . 'BundleController{

      public function indexAction(){

              $this->forwardToController("' . $this->name . '_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All ' . $this->name . '";

              $' . $this->name . ' = new ' . $this->name . '();

              //Used by the HTMLGenerator in the list view.
              $params[\'table\'] = array(

                \'class\' => \'paginate\',
                \'title\' => \'Dataset\',
                \'tbody\' => $' . $this->name . '->GetAll(array(\'order by\' => \'id desc\')),
                \'ignoreFields\' => array(),
                \'actions\' => array(

                    \'Edit\' => array(

                        \'route\' => \'' . $this->name . '_Edit\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '__id\',
                    ),

                    \'View\' => array(

                        \'route\' => \'' . $this->name . '_View\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '__id\',
                    ),

                    \'Delete\' => array(

                        \'message\' => \'Are you sure you want to delete this record?\',
                        \'class\' => \'remove\',
                        \'route\' => \'' . $this->name . '_Delete\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '__id\',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:' . $this->name . ':list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View ' . $this->name . '";

              $' . $this->name . ' = new ' . $this->name . '();

              $params["table"] = array(

                  \'title\' => \'View\',
                  \'class\' => \'paginate\',
                  \'tbody\' => $' . $this->name . '->Get($id),
                  \'actions\' => array(

                      \'Edit\' => array(

                        \'route\' => \'' . $this->name . '_Edit\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '__id\',
                    ),
                  ),
              );

              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:' . $this->name . ':view.html.php", $params);

      }

      public function createAction(){

            if($this->isPost("submit")){

              $' . $this->name . ' = new ' . $this->name . '();

              if($' . $this->name . '->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("' . $this->name . '_List");

            }

            $params["PageTitle"] = "Create New ' . $this->name . '";

            $params[\'form\'] = array(

                \'class\' => \'form\',
                \'action\' => $this->setRoute(\'' . $this->name . '_Create\'),
                \'title\' => \'Random Title\',
                \'inputs\' => array(

                    \'text\' => array(

                        \'label\' => \'Name\',
                        \'name\' => \'Name\',
                        \'value\' => \'Enter your name\',
                    )
                ),
                \'table\' => $'.$this->name.'->GetFormFields(),

                \'submission\' => array(

                    \'submit\' => array(

                        \'value\' => \'Create new record\',
                        \'name\' => \'submit\'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:' . $this->name . ':create.html.php", $params);

      }

      public function editAction($id){

            $' . $this->name . ' = new ' . $this->name . '($id);

            if($this->isPost("submit")){

              if($' . $this->name . '->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("' . $this->name . '_List");

            }

            $params["form"] = array(

                \'title\' => \'Edit\',
                \'action\' => $this->setRoute(\''.$this->name.'_Edit\', array(\'id\' => $id)),
                \'table\' => $'.$this->name.'->Get($id),
                \'submission\' => array(

                    \'submit\' => array(

                        \'value\' => \'Save changes\',
                        \'name\' => \'submit\'
                    ),
                )

            );

            $params["PageTitle"] = "Edit testBundle";

            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:' . $this->name . ':edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->isAjax()){

              $' . $this->name . ' = new ' . $this->name . '();

              if($' . $this->name . '->delete($id))
                  echo \'success:Delete was successful\';
              else
                  echo \'error:Delete was unsuccessful\';
            }
      }
}
              ';

            fwrite($handle, $initController);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Controllers/' . $this->name . 'BundleController.php', 'w+');

        $initController = '<?php

// Use this class to inherit methods used in all or some of your controllers
class ' . $this->name . 'BundleController extends ApplicationController{

}
              ';

            fwrite($handle, $initController);

            fclose($handle);

            return $this;
    }

    private function createRoutes(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Routes');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Routes/' . $this->name . '.php', 'w+');

        $initRoute = '<?php

Router::$Route[\'' . $this->name . '\'] = array(

      "Controller" => "' . $this->name . ':index",
      "Pattern" => "/' . $this->name . '/"
);

Router::$Route[\'' . $this->name . '_List\'] = array(

      "Controller" => "' . $this->name . ':list",
      "Pattern" => "/' . $this->name . '/List/"
);

Router::$Route[\'' . $this->name . '_View\'] = array(

      "Controller" => "' . $this->name . ':view",
      "Pattern" => "/' . $this->name . '/View/{id}/"
);

Router::$Route[\'' . $this->name . '_Create\'] = array(

      "Controller" => "' . $this->name . ':create",
      "Pattern" => "/' . $this->name . '/Create/"
);

Router::$Route[\'' . $this->name . '_Edit\'] = array(

      "Controller" => "' . $this->name . ':edit",
      "Pattern" => "/' . $this->name . '/Edit/{id}/"
);

Router::$Route[\'' . $this->name . '_Delete\'] = array(

      "Controller" => "' . $this->name . ':delete",
      "Pattern" => "/' . $this->name . '/Delete/{id}/"
);
              ';

            fwrite($handle, $initRoute);

            fclose($handle);

            return $this;
    }

    private function CreateAssets(){

        mkdir(ASSETS_FOLDER . 'Bundles/' . $this->name);
        mkdir(ASSETS_FOLDER . 'Bundles/' . $this->name . '/Images');
        mkdir(ASSETS_FOLDER . 'Bundles/' . $this->name . '/JS');
        mkdir(ASSETS_FOLDER . 'Bundles/' . $this->name . '/CSS');

        $handle = fopen(ASSETS_FOLDER . 'Bundles/' . $this->name . '/JS/' . $this->name . '.js', 'w+');

        $initRoute = '/* Javascript for '.$this->name.' Bundle*/
';

            fwrite($handle, $initRoute);

            fclose($handle);

        $handle = fopen(ASSETS_FOLDER . 'Bundles/' . $this->name . '/CSS/' . $this->name . '.css', 'w+');

        $initRoute = '/* Stylesheet for '.$this->name.' Bundle*/

root{

}';

            fwrite($handle, $initRoute);

            fclose($handle);
    }

}