<?php

class Bundle extends Console {

    public function createBundle() {

        echo 'Enter name of the bundle you want to create: ';

        $bundleName = $this->readUser();

        define('CREATE_NEW_BUNDLE', $bundleName);

        if (mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE)) {

            mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Controllers');

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Controllers/' . CREATE_NEW_BUNDLE . 'Controller.php', 'w+');

            $initController = '<?php

class ' . CREATE_NEW_BUNDLE . 'Controller extends Application{

      public function indexAction(){

              $this->forwardToController("' . CREATE_NEW_BUNDLE . '_List");

      }

      public function listAction(){

              $params["PageTitle"] = "All ' . CREATE_NEW_BUNDLE . '";

              $' . CREATE_NEW_BUNDLE . ' = new ' . CREATE_NEW_BUNDLE . '();

              $params["data"] = $' . CREATE_NEW_BUNDLE . '->GetAll();

              $this->Render("Bundle:' . CREATE_NEW_BUNDLE . ':list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View ' . CREATE_NEW_BUNDLE . '";

              $' . CREATE_NEW_BUNDLE . ' = new ' . CREATE_NEW_BUNDLE . '();

              $params["data"] = $' . CREATE_NEW_BUNDLE . '->Get($id);

              $this->Render("Bundle:' . CREATE_NEW_BUNDLE . ':view.html.php", $params);

      }

      public function createAction(){

            if(isset($_POST["submit"])){

              $' . CREATE_NEW_BUNDLE . ' = new ' . CREATE_NEW_BUNDLE . '();

              if($' . CREATE_NEW_BUNDLE . '->create())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("' . CREATE_NEW_BUNDLE . '_List");

            }

            $params["PageTitle"] = "Create New ' . CREATE_NEW_BUNDLE . '";

            $this->Render("Bundle:' . CREATE_NEW_BUNDLE . ':create.html.php");

      }

      public function editAction($id){

            $' . CREATE_NEW_BUNDLE . ' = new ' . CREATE_NEW_BUNDLE . '($id);

            if(isset($_POST["submit"])){

              if($' . CREATE_NEW_BUNDLE . '->update())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("' . CREATE_NEW_BUNDLE . '_List");

            }

            $params["data"] = $' . CREATE_NEW_BUNDLE . '->Get($id);

            $params["PageTitle"] = "Edit {$params["data"]->name}";

            $this->Render("Bundle:' . CREATE_NEW_BUNDLE . ':edit.html.php", $params);

      }

      public function deleteAction($id){

            if(isset($_POST["submit"])){

              $' . CREATE_NEW_BUNDLE . ' = new ' . CREATE_NEW_BUNDLE . '($id);

              if($' . CREATE_NEW_BUNDLE . '->delete())
                  $this->setFlash(array("Success" => "Delete successful."));
              else
                  $this->setError(array("Failure" => "Failed to delete."));
            }


            $this->forwardTo("' . CREATE_NEW_BUNDLE . '_List");

      }
}
              ';

            fwrite($handle, $initController);

            fclose($handle);

            mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Routes');

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Routes/' . CREATE_NEW_BUNDLE . '.php', 'w+');

            $initRoute = '<?php

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':index",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/"

);

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '_List\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':list",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/List/"

);

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '_View\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':view",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/View/{id}/"

);

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '_Create\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':create",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/Create/"

);

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '_Edit\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':edit",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/Edit/{id}/"

);

$_SESSION[\'Routes\'][\'' . CREATE_NEW_BUNDLE . '_Delete\'] = array(

      "Controller" => "' . CREATE_NEW_BUNDLE . ':delete",
      "Pattern" => "/' . CREATE_NEW_BUNDLE . '/Delete/{id}/"

);
              ';

            fwrite($handle, $initRoute);

            fclose($handle);

            mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Configs');

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Configs/' . CREATE_NEW_BUNDLE . '.php', 'w+');

            $initTemplate = '';

            fwrite($handle, $initTemplate);

            fclose($handle);

            mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates');

            mkdir(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/ControllerViews');

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/' . 'Header.html.php', 'w+');

            $initTemplate = '<?=$this->RenderTemplate("Templates::Header.html.php", $params)?>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/' . 'Footer.html.php', 'w+');

            $initTemplate = ' <?=$this->RenderTemplate("Templates::Footer.html.php", $params)?>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/ControllerViews/list.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <h1>List</h1>

    <table>

        <thead>

            <tr>

                <th>ID</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach($params["data"] as $entity): ?>

                <tr>

                    <td><?=$entity->id?></td>

                </tr>

            <?php endforeach; ?>

        </tbody>

        <tfoot>

            <tr>

                <td></td>

            </tr>

        </tfoot>

    </table>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/ControllerViews/view.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <h1>View</h1>

    <table>

        <thead>

            <tr>

                <th>ID</th>

            </tr>

        </thead>

        <tbody>

            <tr>

                <td><?=$params["data"]->id?></td>

            </tr>

        </tbody>

        <tfoot>

            <tr>

                <td></td>

            </tr>

        </tfoot>

    </table>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/ControllerViews/create.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <h1>Create</h1>

    <form method="post" action="<?=$this->setRoute(\'' . CREATE_NEW_BUNDLE . '_Create\')?>" class="form">

        <div class="formRow">

            <label></label>

            <div class="rowRight">

                <input type="text" value="" name="">

            </div>

        </div>

        <div class="buttons">

            <input type="submit" value="Submit Form" name="submit">

        </div>

    </form>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/Templates/ControllerViews/edit.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <h1>Edit</h1>

    <form method="POST" action="<?=$this->setRoute(\'' . CREATE_NEW_BUNDLE . '_Edit\')?>" class="form">

        <div class="formRow">

            <label></label>

            <div class="rowRight">

                <input type="text" value="" name="">

            </div>

        </div>

        <div class="buttons">

            <input type="submit" value="Submit Form" name="submit">

        </div>

    </form>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . CREATE_NEW_BUNDLE . '/' . 'Entity.php', 'w+');

            $initEntity = '<?php

class ' . CREATE_NEW_BUNDLE . ' extends Application{

      protected
                  $id,
                  $title,
                  $selectTableIdentifiers,
                  $selectJoinQuery,
                  $tableName;

      public function __construct($id = null){

         parent::__construct();

         $this->selectTableIdentifiers = "*";

         $this->selectJoinQuery = ""; //Instead of a nested select, using join in queries will dramatically increase your application\'s performance

         $this->tableName = "' . CREATE_NEW_BUNDLE . '";

         if(!empty($id) && is_numeric($id))
            $this->Get($id);

      }

      public function GetAll(){

        $sql = "select {$this->selectTableIdentifiers} from {$this->tableName} {$this->selectJoinQuery}";

        if($this->activeConnection->Query($sql));
            return $this->activeConnection->queryResult;

      }

      public function Get($id){

        $sql = "select {$this->selectTableIdentifiers} from {$this->tableName} {$this->selectJoinQuery} where id = $id";

        if($this->activeConnection->Query($sql));
            return $this->activeConnection->queryResult;

      }

      public function Create($params = null){

        if(!empty($params)){

            $this->title = $params["title"];

        }

        $sql = "insert into {$this->tableName} (title) values ({$this->title})";

        if($this->activeConnection->Query($sql));
            return $this->activeConnection->rowsAffected;

      }

      public function Update($id){

        $sql = "update {$this->tableName} set - = \'-\' where id = $id";

        if($this->activeConnection->Query($sql));
            return $this->activeConnection->rowsAffected;

      }

      public function Delete($id){

        $sql = "delete from {$this->tableName} where id = $id";

        if($this->activeConnection->Query($sql));
            return $this->activeConnection->rowsAffected;

      }
}
              ';

            fwrite($handle, $initEntity);

            fclose($handle);
        }

        echo 'Bundle ' . $bundleName . ' has been created successfully!';
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

}