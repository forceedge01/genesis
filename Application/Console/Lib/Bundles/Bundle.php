<?php

namespace Application\Console\Libraries;



use Application\Console\Console;

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

            $this->createConfig()->createRoutes()->createInterface()->createController()->createEntity()->createViews() -> createTests() ->CreateAssets();
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
            if ($this->removeDirectory(BUNDLES_FOLDER . $bundleName)){

                if(is_dir(CONSOLE_BUNDLES_ASSETS_FOLDER .  $bundleName)){

                    $this->removeDirectory(CONSOLE_BUNDLES_ASSETS_FOLDER . $bundleName);
                }

                echo 'Bundle has been deleted successfully.';
            }
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

        mkdir(BUNDLES_FOLDER . $this->name . '/Resources');

        mkdir(BUNDLES_FOLDER . $this->name . '/Resources/Configs');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Configs/' . $this->name . '.Config.php', 'w+');

        $initTemplate = '<?php

Set::Config(\'BUNDLE_'.strtoupper($this->name).'_PATH\', BUNDLES_FOLDER . \''.$this->name.'\');';

        fwrite($handle, $initTemplate);

        fclose($handle);

        return $this;
    }

    private function createEntity(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Model');

        mkdir(BUNDLES_FOLDER . $this->name . '/Model/Entities');

        mkdir(BUNDLES_FOLDER . $this->name . '/Model/Repositories');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Model/Entities/' . $this->name . 'Entity.php', 'w+');

        $initEntity = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Entities;



use \\Application\\Core\\Entities\\ApplicationEntity;

// This Entity represents '.$this->name.' table

final class ' . $this->name . 'Entity extends ApplicationEntity {

}
              ';

        fwrite($handle, $initEntity);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Model/Repositories/' . $this->name . 'Repository.php', 'w+');

        $initEntity = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Repositories;



use \\Application\\Core\\Repositories\\ApplicationRepository;

use \\Application\\Bundles\\'.$this->name.'\\Interfaces\\'.$this->name.'RepositoryInterface;

// This Repository holds methods to query '.$this->name.' table

final class ' . $this->name . 'Repository extends ApplicationRepository implements '.$this->name.'Repository.Interface{

}
              ';

        fwrite($handle, $initEntity);

        fclose($handle);

        return $this;
    }

    private function createViews(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Resources/Views');

        mkdir(BUNDLES_FOLDER . $this->name . '/Resources/Views/ControllerViews');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/' . 'Header.html.php', 'w+');

        $initTemplate = '<?=$this->RenderTemplate(":Header.html.php", $params)?>

<?=$this->setAsset("'.$this->name.':'.$this->name.'.js")?>
<?=$this->setAsset("'.$this->name.':'.$this->name.'.css")?>

';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/' . 'Footer.html.php', 'w+');

        $initTemplate = ' <?=$this->RenderTemplate(":Footer.html.php", $params)?>

<?=$this->setAsset("'.$this->name.':'.$this->name.'.js")?>
';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/ControllerViews/list.html.php', 'w+');

        $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_Create\')?>">Create new '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/ControllerViews/view.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($table, \'table\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/ControllerViews/create.html.php', 'w+');

            $initTemplate = '<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute(\'' . $this->name . '_List\')?>">View All '.$this->name.'</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($form, \'form\')?>

    </div>

</div>';

            fwrite($handle, $initTemplate);

            fclose($handle);

            $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Views/ControllerViews/edit.html.php', 'w+');

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

    private function createInterface(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Interfaces');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Interfaces/' . $this->name . 'Controller.Interface.php', 'w+');

        $initControllerInterface = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Interfaces;


/**
 *
 * @group groupName
 * @author John Doe <john.doe@example.com>
 *
 */
interface '.$this->name.'ControllerInterface {

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function indexAction();

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function viewAction($id);

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function editAction($id);

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function createAction();

    /**
     *
     * @author <Above>
     *
     * @param type $name Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function deleteAction($id);
}

';

        fwrite($handle, $initControllerInterface);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Interfaces/' . $this->name . 'Repository.Interface.php', 'w+');

        $initControllerInterface = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface '.$this->name.'Repositoryinterface {

}
        ';

        fwrite($handle, $initControllerInterface);

        fclose($handle);

        return $this;
    }

    private function createController(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Controllers');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Controllers/' . $this->name . 'Controller.php', 'w+');

        $initController = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Controllers;



use \\Application\\Bundles\\'.$this->name.'\\Entities\\'.$this->name.'Entity;
use \\Application\\Bundles\\'.$this->name.'\\Repositories\\'.$this->name.'Repository;

use \\Application\\Components\\HTMLGenerator\\HTMLGenerator;

use Application\\Bundles\\' . $this->name . '\\Interfaces\\' . $this->name . 'ControllerInterface;


final class ' . $this->name . 'Controller extends ' . $this->name . 'BundleController implements ' . $this->name . 'ControllerInterface{

      public
            $htmlgen;

      public function indexAction(){

              $this->forwardToController("' . $this->name . '_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All ' . $this->name . '";

              //Used by the HTMLGenerator in the list view.
              $params[\'table\'] = array(

                \'class\' => \'paginate\',
                \'title\' => \'Dataset\',
                \'tbody\' => $this->GetRepository("' . $this->name . ':'.$this->name.'")->GetAll(array(\'order by\' => \'id desc\')),
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
              $this->htmlgen = $this ->GetComponent(\'HTMLGenerator\');

              $this->Render("' . $this->name . ':list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View ' . $this->name . '";

              $params["table"] = array(

                  \'title\' => \'View\',
                  \'class\' => \'paginate\',
                  \'tbody\' => $this->GetEntity("' . $this->name . ':'.$this->name.'")->Get($id),
                  \'actions\' => array(

                      \'Edit\' => array(

                        \'route\' => \'' . $this->name . '_Edit\',
                        \'routeParam\' => \'id\',
                        \'dataParam\' => \'' . $this->name . '__id\',
                    ),
                  ),
              );

              $this->htmlgen = $this ->GetComponent(\'HTMLGenerator\') ;

              $this->Render("' . $this->name . ':view.html.php", $params);

      }

      public function createAction(){

            if($this->Request() ->isPost("submit")){

              if($this->GetEntity("' . $this->name . 'Bundle:'.$this->name.'")->Save())
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
                \'table\' => $this->GetEntity("' . $this->name . ':'.$this->name.'")->GetFormFields(),

                \'submission\' => array(

                    \'submit\' => array(

                        \'value\' => \'Create new record\',
                        \'name\' => \'submit\'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = $this ->GetComponent(\'HTMLGenerator\') ;

            $this->Render("' . $this->name . ':create.html.php", $params);

      }

      public function editAction($id){

            if($this->Request()->isPost("submit")){

              if($'.$this->name.' = $this->getEntity("' . $this->name . 'Bundle:'.$this->name.'")->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("' . $this->name . '_List");

            }

            $params["form"] = array(

                \'title\' => \'Edit\',
                \'action\' => $this->setRoute(\''.$this->name.'_Edit\', array(\'id\' => $id)),
                \'table\' => $this->GetEntity(\''.$this->name.':'.$this->name.'\')->Get($id),
                \'submission\' => array(

                    \'submit\' => array(

                        \'value\' => \'Save changes\',
                        \'name\' => \'submit\'
                    ),
                )

            );

            $params["PageTitle"] = "Edit '.$this->name.'";

            $this->htmlgen = $this ->GetComponent(\'HTMLGenerator\') ;

            $this->Render("' . $this->name . ':edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->Request()->isAjax()){

              $'.$this->name.' = $this->getEntity("' . $this->name . ':'.$this->name.'");

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

namespace Application\\Bundles\\'.$this->name.'\\Controllers;



use \\Application\\Core\\Controllers\\ApplicationController;


// Use this class to inherit methods used in all or some of your ' . $this->name . ' bundle controllers
// ' . $this->name . ' bundle created at: ' . date('l, d F, Y') . '

class ' . $this->name . 'BundleController extends ApplicationController{

}
              ';

            fwrite($handle, $initController);

            fclose($handle);

            return $this;
    }

    private function createRoutes(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Resources/Routes');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Resources/Routes/' . $this->name . '.Routes.php', 'w+');

        $initRoute = '<?php


Set::Route(\'' . $this->name . '\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':index",
      "Pattern" => "/' . $this->name . '/"
));

Set::Route(\'' . $this->name . '_List\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':list",
      "Pattern" => "/' . $this->name . '/List/"
));

Set::Route(\'' . $this->name . '_View\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':view",
      "Pattern" => "/' . $this->name . '/View/{id}/"
));

Set::Route(\'' . $this->name . '_Create\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':create",
      "Pattern" => "/' . $this->name . '/Create/"
));

Set::Route(\'' . $this->name . '_Edit\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':edit",
      "Pattern" => "/' . $this->name . '/Edit/{id}/"
));

Set::Route(\'' . $this->name . '_Delete\', array(

      "Controller" => "' . $this->name . ':' . $this->name . ':delete",
      "Pattern" => "/' . $this->name . '/Delete/{id}/"
));
              ';

            fwrite($handle, $initRoute);

            fclose($handle);

            return $this;
    }

    private function CreateAssets(){

        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name);
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/Images');
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/JS');
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/CSS');

        $handle = fopen(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/JS/' . $this->name . '.js', 'w+');

        $initRoute = '/* Javascript for '.$this->name.' Bundle */

jQuery(document).ready(function(){

});

';

            fwrite($handle, $initRoute);

            fclose($handle);

        $handle = fopen(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/CSS/' . $this->name . '.css', 'w+');

        $initRoute = '/* Stylesheet for '.$this->name.' Bundle */

root{
    font-size: 12px;
    font-family: verdana;
    color: black;
}';

            fwrite($handle, $initRoute);

            fclose($handle);

            return $this;
    }

    private function createTests(){

        mkdir(BUNDLES_FOLDER . $this->name . '/Tests');

        mkdir(BUNDLES_FOLDER . $this->name . '/Tests/Config');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Tests/Config/' . $this->name . '.Test.Config.php', 'w+');

        $initTemplate = '<?php
            ';

        fwrite($handle, $initTemplate);

        fclose($handle);

        mkdir(BUNDLES_FOLDER . $this->name . '/Tests/Scenarios');

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Tests/Scenarios/' . $this->name . 'Controller.Test.php', 'w+');

        $initTemplate = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Tests;



use Application\\Console\\BaseTestingRoutine;

class Test'.$this -> name.'Controller extends BaseTestingRoutine
{
    public function testIndexAction()
    {
        self::$testClass = new \\Application\\Bundles\\'.$this->name.'\\Controllers\\'.$this->name.'Controller();

        $method = \'IndexAction\';

        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array(\'case\' => \'string\'));
    }
}';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Tests/Scenarios/' . $this->name . 'Entity.Test.php', 'w+');

        $initTemplate = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Tests;



use Application\\Console\\BaseTestingRoutine;

class Test'.$this -> name.'Entity extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::$testClass = new \\Application\\Bundles\\'.$this->name.'\\Entities\\'.$this->name.'Entity();

        $method = \'\';

        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array(\'case\' => \'string\'));
    }
}';

        fwrite($handle, $initTemplate);

        fclose($handle);

        $handle = fopen(BUNDLES_FOLDER . $this->name . '/Tests/Scenarios/' . $this->name . 'Repository.Test.php', 'w+');

        $initTemplate = '<?php

namespace Application\\Bundles\\'.$this->name.'\\Tests;



use Application\\Console\\BaseTestingRoutine;

class Test'.$this -> name.'Repository extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::$testClass = new \\Application\\Bundles\\'.$this->name.'\\Repositories\\'.$this->name.'Repository();

        $method = \'\';

        //Checks if the returned value of this function is an integer
        $this ->AssertTrue($method, array(\'case\' => \'array\'));
    }
}';

        fwrite($handle, $initTemplate);

        fclose($handle);

        return $this;
    }

}