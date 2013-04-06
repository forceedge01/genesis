<?php

class Bundle extends Console {
    
    private 
            $name;

    public function createBundle() {

        echo 'Enter name of the bundle you want to create: ';

        $this->name = $this->readUser();

        if (mkdir(BUNDLES_FOLDER . $this->name)) {

            $this->createConfig()->createRoutes()->createController()->createEntity()->createViews();
        }

        echo 'Bundle ' . $this->name . ' has been created successfully!';
    }

    public function deleteBundle() {

        $this->linebreak(1);

        echo 'Bundles you have in your application: ';

        $this->linebreak(1);

        $this->readBundles();

        $this->linebreak(1);

        echo 'Enter name of the bundle you want to delete: ';

        $bundleName = $this->readUser();
        $this->linebreak(1);

        if ($this->removeDirectory(BUNDLES_FOLDER . $bundleName))
            echo 'Bundle has been deleted successfully.';
        else
            echo 'Unable to delete bundle.';
    }

    private function readBundles() {

        $bundles = scandir(BUNDLES_FOLDER);

        foreach ($bundles as $bundle) {

            if (is_dir(BUNDLES_FOLDER . $bundle)) {

                if($bundle != '.' && $bundle != '..') {

                    echo $bundle;
                    $this->linebreak(1);
                }
            }
        }
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
        
        $handle = fopen(BUNDLES_FOLDER . $this->name . '/' . 'Entity.php', 'w+');

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

         $this->joinQuery = ""; //Instead of a nested select, using join in queries will dramatically increase your application\'s performance

         $this->tableName = \'' . $this->name . '\';

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
      
        //return $this->GetActiveConnection()->Query("select {$this->tableColumns} from {$this->tableName} {$this->joinQuery}")->GetResultSet();

        return $this->GetActiveConnection()->Table($this->tableName, $this->tableColumns)->GetRecords($params)->GetResultSet();

      }

      /**
       *
       * @param Mixed $id Can be the primary key value or an array of column and values
       * @return mixed Returns the matching data set from the database.
       */
      public function Get($id = null){

        if(!$id)
            $id = $this->id;
            
        return $this->GetActiveConnection()->Table($this->tableName, $this->tableColumns)->GetOneRecordBy($id);

      }

      /**
       *
       * @param array $params Pass in the data for saving it to the database, if not provided<br>
       * the submitted data in globals will be taken and matched to the table on which the operation is applied.
       */
      public function Save(array $params = array()){

        return $this->GetActiveConnection()->Table($this->tableName)->SaveRecord($params)->GetAffectedRows();

      }

      /**
       *
       * @param int $id the id of the record to be deleted
       * @return int Number of rows affected
       */
      public function Delete($id = null){
      
        if(!$id)
            $id = $this->id;

        return $this->GetActiveConnection()->Table($this->tableName)->DeleteRecord($id)->GetAffectedRows();

      }
}
              ';

        fwrite($handle, $initEntity);

        fclose($handle);
        
        return $this;
    }
    
    private function createViews(){
        
        mkdir(BUNDLES_FOLDER . $this->name . '/Templates');

        mkdir(BUNDLES_FOLDER . $this->name . '/Templates/ControllerViews');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/' . 'Header.html.php', 'w+');

        $initTemplate = '<?=$this->RenderTemplate("Templates::Header.html.php", $params)?>';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/' . 'Footer.html.php', 'w+');

        $initTemplate = ' <?=$this->RenderTemplate("Templates::Footer.html.php", $params)?>';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/ControllerViews/list.html.php', 'w+');

        $initTemplate = '<div class="wrapper">
            
    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_Create\')?>">Create new '.$this->name.'</a></div>
                
    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>
    
    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/ControllerViews/view.html.php', 'w+');

            $initTemplate = '<div class="wrapper">
                
    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>
                
    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>
    
    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/ControllerViews/create.html.php', 'w+');

            $initTemplate = '<div class="wrapper">
                
    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($form, \'form\')?>
    
    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Templates/ControllerViews/edit.html.php', 'w+');

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

class ' . $this->name . 'Controller extends Application{

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
                        \'dataParam\' => \'' . $this->name . '\',
                    ),
                
                    \'View\' => array(
                        
                        \'route\' => \'' . $this->name . '_View\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '\',
                    ),
                    
                    \'Delete\' => array(
                        
                        \'message\' => \'Are you sure you want to delete this record?\',
                        \'class\' => \'remove\',
                        \'route\' => \'' . $this->name . '_Delete\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '\',
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
                        \'dataParam\' => \'' . $this->name . '\',
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
                \'data\' => $'.$this->name.'->Get($id),
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
            
            return $this;
    }
    
    private function createRoutes(){
        
        mkdir(BUNDLES_FOLDER . $this->name . '/Routes');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Routes/' . $this->name . '.php', 'w+');

        $initRoute = '<?php

$_SESSION[\'Routes\'][\'' . $this->name . '\'] = array(

      "Controller" => "' . $this->name . ':index",
      "Pattern" => "/' . $this->name . '/"

);

$_SESSION[\'Routes\'][\'' . $this->name . '_List\'] = array(

      "Controller" => "' . $this->name . ':list",
      "Pattern" => "/' . $this->name . '/List/"

);

$_SESSION[\'Routes\'][\'' . $this->name . '_View\'] = array(

      "Controller" => "' . $this->name . ':view",
      "Pattern" => "/' . $this->name . '/View/{id}/"

);

$_SESSION[\'Routes\'][\'' . $this->name . '_Create\'] = array(

      "Controller" => "' . $this->name . ':create",
      "Pattern" => "/' . $this->name . '/Create/"

);

$_SESSION[\'Routes\'][\'' . $this->name . '_Edit\'] = array(

      "Controller" => "' . $this->name . ':edit",
      "Pattern" => "/' . $this->name . '/Edit/{id}/"

);

$_SESSION[\'Routes\'][\'' . $this->name . '_Delete\'] = array(

      "Controller" => "' . $this->name . ':delete",
      "Pattern" => "/' . $this->name . '/Delete/{id}/"

);
              ';

            fwrite($handle, $initRoute);

            fclose($handle);
            
            return $this;
    }

}