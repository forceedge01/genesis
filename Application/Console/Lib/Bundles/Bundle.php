<?php

namespace Application\Console\Libraries;



use Application\Console\Console;

class Bundle extends Console {

    public
            $name,
            $renderMethod,
            $singular,
            $bundleFolder;

    public function __construct($type) {

        $this->renderMethod = $type;
    }

    public function createBundle() {

        if(!isset($_SERVER['SERVER_NAME'])){

            echo 'Enter name of the bundle you want to create (If you are using a database with this application, this is usually the singular form of your table name): ';

            $this->name = $this->singular = str_replace('bundle', '', strtolower($this->readUser()));

            if(substr($this->singular, -1) == 's')
                  $this->singular = substr($this->singular, 0, -1);

        }

        $this->bundleFolder = $this->bundleFolder;

        if (mkdir($this->bundleFolder)) {

            $this->createConfig()->createRoutes()->createInterface()->createController()->createEntity()->createModel()->createViews() -> createTests() ->CreateAssets();
        }

        echo "Bundle {$this->name} has been created successfully!";

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

        mkdir($this->bundleFolder . '/Resources');
        mkdir($this->bundleFolder . '/Resources/Configs');

        $initTemplate = "<?php

Set::Config('BUNDLE_".strtoupper($this->name)."_PATH', BUNDLES_FOLDER{$this->name});";

        $this->createFile($this->bundleFolder . "/Resources/Configs/{$this->name}.Config.php", $initTemplate);

        return $this;
    }

    private function createEntity(){

        mkdir($this->bundleFolder . '/Model');
        mkdir($this->bundleFolder . '/Model/Entities');
        mkdir($this->bundleFolder . '/Model/Repositories');

        $initEntity = "<?php

namespace Bundles\\{$this->name}\\Entities;



use \\Application\\Core\\Entities\\ApplicationEntity;

// This Entity represents {$this->name} table

final class {$this->name}Entity extends ApplicationEntity {

}
";

        $this->createFile($this->bundleFolder . "/Model/Entities/{$this->name}Entity.php", $initEntity);

        $initRepository = "<?php

namespace Bundles\\{$this->name}\\Repositories;



use \\Application\\Core\\Repositories\\ApplicationRepository;

use \\Application\\Bundles\\{$this->name}\\Interfaces\\{$this->name}RepositoryInterface;

// This Repository holds methods to query {$this->name} table

final class {$this->name}Repository extends ApplicationRepository implements {$this->name}Repository.Interface{

}
              ";

        $this->createFile($this->bundleFolder . "/Model/Repositories/{$this->name}Repository.php", $initRepository);

        return $this;
    }

    private function createModel(){

        $Model = "<?php

namespace Bundles\\{$this->name}\\Models;




// Model represents the logic of {$this->name} table with the application

final class {$this->name}Model implements {$this->name}ModelInterface{

    public function Create{$this->singular}()
    {
        if (\$this->GetEntityObject()->Save(\$this->entityObject))
            return true;

        return false;
    }

    public function Update{$this->singular}()
    {
        if (\$this->GetEntityObject()->Save())
            return true;

        return false;
    }

    public function Delete{$this->singular}()
    {
        if (\$this->GetEntityObject()->Delete())
            return true;

        return false;
    }
}";

        $this->createFile($this->bundleFolder . "/Model/{$this->name}Model.php", $Model);

        return $this;
    }

    private function createViews(){

        mkdir($this->bundleFolder . '/Resources/Views');
        mkdir($this->bundleFolder . '/Resources/Views/ControllerViews');

        $initTemplate = "<?=\$this->IncludeTemplate(':Header.html.php', \$params)?>
<?=\$this->setAsset('{$this->name}:{$this->name}.css')?>
";

        $this->createFile($this->bundleFolder . '/Resources/Views/' . 'Header.html.php', $initTemplate);

        $initTemplate = "<?=\$this->setAsset('{$this->name}:{$this->name}.js')?>
<?=\$this->IncludeTemplate(':Footer.html.php', \$params)?>
";

        $this->createFile($this->bundleFolder . '/Resources/Views/' . 'Footer.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_Create')?>'>Create new {$this->singular}</a>
    </div>
    <h3>List of all {$this->name}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$table, 'table')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . '/Resources/Views/ControllerViews/list.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>View {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$table, 'table')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . '/Resources/Views/ControllerViews/view.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>Create new {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$form, 'form')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . '/Resources/Views/ControllerViews/create.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>Edit {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$form, 'form')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . '/Resources/Views/ControllerViews/edit.html.php', $initTemplate);

        return $this;
    }

    private function createInterface(){

        mkdir($this->bundleFolder . '/Interfaces');

        $initControllerInterface = "<?php

namespace Bundles\\{$this->name}\\Interfaces;


/**
 *
 * @group groupName
 * @author John Doe <john.doe@example.com>
 *
 */
interface {$this->name}ControllerInterface {

    /**
     *
     * @author <Above>
     *
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
     * @param type \$id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function viewAction(\$id);

    /**
     *
     * @author <Above>
     *
     * @param type \$id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function editAction(\$id);

    /**
     *
     * @author <Above>
     *
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
     * @param type \$id Description
     * @return type Description
     *
     * @example path description
     *
     */
    public function deleteAction(\$id);
}
";

        $this->createFile($this->bundleFolder . "/Interfaces/{$this->name}Controller.Interface.php", $initControllerInterface);

        $initControllerInterface = "<?php

namespace Bundles\\{$this->name}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}RepositoryInterface {

}
";

        $this->createFile($this->bundleFolder . "/Interfaces/{$this->name}Repository.Interface.php", $initControllerInterface);

        $initControllerInterface = "<?php

namespace Bundles\\{$this->name}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}ModelInterface {

}
";

        $this->createFile($this->bundleFolder . "/Interfaces/{$this->name}Model.Interface.php", $initControllerInterface);

        return $this;
    }

    private function createController(){

        mkdir($this->bundleFolder . '/Controllers');

        $initController = "<?php

namespace Bundles\\{$this->name}\\Controllers;



use \\Application\\Components\\HTMLGenerator\\HTMLGenerator;

use \\Bundles\\{$this->name}\\Entities\\{$this->name}Entity;
use \\Bundles\\{$this->name}\\Repositories\\{$this->name}Repository;

use \\Bundles\\{$this->name}\\Interfaces\\{$this->name}ControllerInterface;


final class {$this->name}Controller extends {$this->name}BundleController implements {$this->name}ControllerInterface{

    public
          \$htmlgen;

    public function indexAction()
    {
        \$this->forwardToController('{$this->name}_List');
    }

    public function listAction()
    {
        //Used by the HTMLGenerator in the list view.
        \$params['table'] = array(

          'class' => 'paginate',
          'title' => 'Dataset',
          'tbody' => \$this
                          ->GetRepository('{$this->name}:{$this->name}')
                              ->GetAll(array('order by' => 'id desc')),
          'ignoreFields' => array(),
          'actions' => array(

              'Edit' => array(

                  'route' => '{$this->name}_Edit',
                  'routeParam' => 'id',
                  'dataParam' => '{$this->name}__id',
              ),

              'View' => array(

                  'route' => '{$this->name}_View',
                  'routeParam' => 'id',
                  'dataParam' => '{$this->name}__id',
              ),

              'Delete' => array(

                  'message' => 'Are you sure you want to delete this record?',
                  'class' => 'remove',
                  'route' => '{$this->name}_Delete',
                  'routeParam' => 'id',
                  'dataParam' => '{$this->name}__id',
              ),
          )

        );

        //This will be used in the template to generate the above declared table.
        \$this->htmlgen = \$this ->GetComponent('HTMLGenerator');

        \$this->Render('{$this->name}:list.html.php', 'List {$this->name}', \$params);

    }

    public function viewAction(\$id)
    {
        \${$this->name}Model = new \Bundles\{$this->name}\Models\{$this->name}Model();
        \${$this->name}Model->SetEntity('{$this->name}:{$this->name}');

        \$params['table'] = array(

            'title' => 'View',
            'class' => 'paginate',
            'tbody' => \${$this->name}Model
                        ->GetEntityObject()
                            ->Get(\$id),
            'actions' => array(

                'Edit' => array(

                  'route' => '{$this->name}_Edit',
                  'routeParam' => 'id',
                  'dataParam' => '{$this->name}__id',
              ),
            ),
        );

        \$this->htmlgen = \$this ->GetComponent('HTMLGenerator') ;

        \$this->Render('{$this->name}:view.html.php', 'View {$this->singular}', \$params);
    }

    public function createAction()
    {
        \${$this->name}Model = new \Bundles\{$this->name}\Models\{$this->name}Model();
        \${$this->name}Model->SetEntity('{$this->name}:{$this->name}');

        if(\$this->GetRequestManager()->isPost('submit'))
        {
            if (!\${$this->name}Model->SetEntity('{$this->name}:{$this->name}', \$this->GetRequestManager()->PostParams()))
            {
                \$this->setError('Empty data passed');
            }

            if(\${$this->name}Model->Create{$this->singular}())
            {
                \$this->setFlash(array('Success' => 'Create successful.'));
            }
            else
            {
                \$this->setError(array('Failure' => 'Failed to create.'));
            }

            \$this->forwardTo('{$this->name}_List');
        }

        \$params['form'] = array(

            'class' => 'form',
            'action' => \$this->setRoute('{$this->name}_Create'),
            'title' => 'Random Title',
            'inputs' => array(

                'text' => array(

                    'label' => 'Name',
                    'name' => 'Name',
                    'value' => 'Enter your name',
                )
            ),
            'table' => \${$this->name}Model
                        ->GetEntityObject()
                            ->GetFormFields(),

            'submission' => array(

                'submit' => array(

                    'value' => 'Create new record',
                    'name' => 'submit'
                ),
            ),

        );

        //This will be used in the template to generate the above declared form.
        \$this->htmlgen = \$this ->GetComponent('HTMLGenerator') ;

        \$this->Render('{$this->name}:create.html.php', 'Create new {$this->singular}', \$params);
    }

    public function editAction(\$id)
    {
        \${$this->name}Model = new \Bundles\{$this->name}\Models\{$this->name}Model();
        \${$this->name}Model->SetEntity('{$this->name}:{$this->name}');

        if(\$this->GetRequestManager()->isPost('submit'))
        {
            \${$this->name}Model->SetEntity('{$this->name}:{$this->name}', \$this->GetRequestManager()->PostParams());

            if(\${$this->name}Model->Update{$this->singular}())
            {
                \$this->setFlash(array('Success' => 'Update successful.'));
            }
            else
            {
                \$this->setError(array('Failure' => 'Failed to update.'));
            }

            \$this->forwardTo('{$this->name}_List');
        }

        \$params['form'] = array(

            'title' => 'Edit',
            'action' => \$this->setRoute('{$this->name}_Edit', array('id' => \$id)),
            'table' => \${$this->name}Model
                        ->GetEntityObject()
                            ->Get(\$id),
            'submission' => array(

                'submit' => array(

                    'value' => 'Save changes',
                    'name' => 'submit'
                ),
            )

        );

        \$this->htmlgen = \$this ->GetComponent('HTMLGenerator') ;

        \$this->Render('{$this->name}:edit.html.php', 'Edit {$this->singular}', \$params);
    }

    /**
     *
     * @param int \$id the id to delete from the database
     * By default is ajax controlled.
     *
     */
    public function deleteAction(\$id)
    {
        if(\$this->GetRequestManager()->isAjax())
        {
            \${$this->name}Entity = new {$this->name}Entity(\$id);
            ${$this->name}Model = new \Bundles\{$this->name}\Models\{$this->name}Model(\${$this->name}Entity);

            \${$this->name} = \$this->getEntity('{$this->name}:{$this->name}');

            if(\${$this->name}Model->Delete{$this->singular}())
            {
                echo 'success:Delete was successful';
            }
            else
            {
                echo 'error:Delete was unsuccessful';
            }
        }
    }
}

";

        $this->createFile($this->bundleFolder . "/Controllers/{$this->name}Controller.php", $initController);

        $initController = "<?php

namespace Bundles\\{$this->name}\\Controllers;



use \\Application\\Core\\Controllers\\ApplicationController;


// Use this class to inherit methods used in all or some of your {$this->name} bundle controllers
// {$this->name} bundle created at: " . date('l, d F, Y') . "

class {$this->name}BundleController extends ApplicationController{

}

";

        $this->createFile("/Controllers/{$this->name}BundleController.php", $initController);

        return $this;
    }

    private function createRoutes(){

        mkdir($this->bundleFolder . '/Resources/Routes');

        $initRoute = "<?php


Set::Route('{$this->name}', array(

      'Controller' => '{$this->name}:{$this->name}:index',
      'Pattern' => '/{$this->name}/'
));

Set::Route('{$this->name}_List', array(

      'Controller' => '{$this->name}:{$this->name}:list',
      'Pattern' => '/{$this->name}/List/'
));

Set::Route('{$this->name}_View', array(

      'Controller' => '{$this->name}:{$this->name}:view',
      'Pattern' => '/{$this->name}/View/{id}/'
));

Set::Route('{$this->name}_Create', array(

      'Controller' => '{$this->name}:{$this->name}:create',
      'Pattern' => '/{$this->name}/Create/'
));

Set::Route('{$this->name}_Edit', array(

      'Controller' => '{$this->name}:{$this->name}:edit',
      'Pattern' => '/{$this->name}/Edit/{id}/'
));

Set::Route('{$this->name}_Delete', array(

      'Controller' => '{$this->name}:{$this->name}:delete',
      'Pattern' => '/{$this->name}/Delete/{id}/'
));
";

        $this->createFile($this->bundleFolder . "/Resources/Routes/{$this->name}.Routes.php", $initRoute);

        return $this;
    }

    private function CreateAssets(){

        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name);
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/Images');
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/JS');
        mkdir(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . '/CSS');

        $initJs = "/* Javascript for {$this->name} Bundle */

jQuery(document).ready(function(){

});

";

        $this->createFile(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . "/JS/{$this->name}.js", $initJs);

        $initCss= "/* Stylesheet for {$this->name} Bundle */

root{
    font-size: 12px;
    font-family: verdana;
    color: black;
}";

        $this->createFile(CONSOLE_BUNDLES_ASSETS_FOLDER . $this->name . "/CSS/{$this->name}.css", $initCss);

        return $this;
    }

    private function createTests(){

        mkdir($this->bundleFolder . '/Tests');
        mkdir($this->bundleFolder . '/Tests/Config');

        $initTests = '<?php';

        $this->createFile($this->bundleFolder . "/Tests/Config/{$this->name}.Test.Config.php", $initTests);

        mkdir($this->bundleFolder . '/Tests/Scenarios');

        $initTests = "<?php

namespace Bundles\\{$this->name}\\Tests;



use Application\\Console\\WebTestCase;

class Test{$this -> name}Controller extends WebTestCase
{
    public function testIndexAction()
    {
        self::\$testClass = new \\Bundles\\{$this->name}\\Controllers\\{$this->name}Controller();

        \$method = 'IndexAction';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'string'));
    }
}";

        $this->createFile($this->bundleFolder . "/Tests/Scenarios/{$this->name}Controller.Test.php", $initTests);

        $initTests = "<?php

namespace Bundles\\{$this->name}\\Tests;



use Application\\Console\\WebTestCase;

class Test{$this -> name}Entity extends WebTestCase
{
    public function testExampleMethod()
    {
        self::\$testClass = new \\Bundles\\{$this->name}\\Entities\\{$this->name}Entity();

        \$method = '';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'string'));
    }
}";

        $this->createFile($this->bundleFolder . "/Tests/Scenarios/{$this->name}Entity.Test.php", $initTests);

        $initTests = "<?php

namespace Bundles\\{$this->name}\\Tests;



use Application\\Console\\WebTestCase;

class Test{$this -> name}Repository extends WebTestCase
{
    public function testExampleMethod()
    {
        self::\$testClass = new \\Bundles\\{$this->name}\\Repositories\\{$this->name}Repository();

        \$method = '';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'array'));
    }
}";

        $this->createFile($this->bundleFolder . "/Tests/Scenarios/{$this->name}Repository.Test.php", $initTests);

        $initTests = "<?php

namespace Bundles\\{$this->name}\\Tests;



use Application\\Console\\WebTestCase;

class Test{$this -> name}Templates extends WebTestCase
{
    public function testTemplateList()
    {
        \$this->AssertTemplate('{$this->name}:list.html.php');
    }

    public function testTemplateCreate()
    {
        \$this->AssertTemplate('{$this->name}:create.html.php');
    }

    public function testTemplateEdit()
    {
        \$this->AssertTemplate('{$this->name}:edit.html.php');
    }

    public function testTemplateView()
    {
        \$this->AssertTemplate('{$this->name}:view.html.php');
    }
}";

        $this->createFile($this->bundleFolder . "/Tests/Scenarios/{$this->name}Templates.Test.php", $initTests);

        $initTests = "<?php

namespace Bundles\\{$this->name}\\Tests;



use Application\\Console\\WebTestCase;

class Test{$this -> name}Model extends WebTestCase
{
    public function testModelMethod()
    {

    }
}";

        $this->createFile($this->bundleFolder . "/Tests/Scenarios/{$this->name}Model.Test.php", $initTests);

        return $this;
    }

}