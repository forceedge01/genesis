<?php

namespace Application\Console\Libraries;


use Application\Console\Console;

Class BundleBuilder extends Console{

    public
            $name,
            $bundle,
            $singular,
            $renderMethod,
            $bundleFolder;

    private
            $AssetsFolder,
            $bundleAssetsFolder,
            $bundleSourceFolder,
            $bundleTestsFolder,
            $bundleViewsFolder,
            $bundleDatabaseFolder,
            $bundleConfigsFolder,
            $bundleControllersFolder,
            $bundleInterfacesFolder,
            $bundleRoutesFolder,
            $bundleHeaderFileName,
            $bundleFooterFileName,
            $bundleEventsFolder,
            $bundleNamespace,
            $bundleDirInAssets;

    public function __construct() {

        $this->bundleSourceFolder = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER');
        $this->AssetsFolder = \Get::Config('APPDIRS.TEMPLATING.ASSETS_FOLDER');
        $this->bundleAssetsFolder = \Get::Config('APPDIRS.BUNDLES.ASSETS_FOLDER');
        $this->bundleTestsFolder = \Get::Config('APPDIRS.BUNDLES.TESTS');
        $this->bundleViewsFolder = \Get::Config('APPDIRS.BUNDLES.VIEWS');
        $this->bundleControllersFolder = \Get::Config('APPDIRS.BUNDLES.CONTROLLERS');
        $this->bundleDatabaseFolder = \Get::Config('APPDIRS.BUNDLES.DATABASE_FILES');
        $this->bundleConfigsFolder = \Get::Config('APPDIRS.BUNDLES.CONFIG');
        $this->bundleInterfacesFolder = \Get::Config('APPDIRS.BUNDLES.INTERFACES');
        $this->bundleRoutesFolder = \Get::Config('APPDIRS.BUNDLES.ROUTES');
        $this->bundleEventsFolder = \Get::Config('APPDIRS.BUNDLES.EVENTS');
        $this->bundleHeaderFileName = \Get::Config('APPDIRS.BUNDLES.BUNDLE_VIEW_HEADER_FILE');
        $this->bundleFooterFileName = \Get::Config('APPDIRS.BUNDLES.BUNDLE_VIEW_FOOTER_FILE');
    }

    public function SetBundle($name)
    {
        $this->name = $name;
        $this->bundleFolder = str_replace('//', '/', $this->bundleSourceFolder . $this->name);
        $this->bundleNamespace = str_replace(array('/', '\\\\'), array('\\', '\\'), $this->name);

        $nameChunks = explode('/', $this->name);
        $this->name = end($nameChunks);
        $this->singular = preg_replace('/s$/i', '', end($nameChunks));
        $this->bundleDirInAssets = str_replace('\\', '/', str_replace('Bundles\\','', $name));

        return $this;
    }

    public function CreateBundle()
    {
        if($this->CreateBundleDirs($this->bundleNamespace, $this->bundleSourceFolder))
        {
            $this->bundleNamespace = 'Bundles\\'.$this->bundleNamespace;

            $this
                ->createConfig()
                ->createRoutes()
                ->createInterface()
                ->createController()
                ->createModel()
                ->createEntity()
                ->createEvent()
                ->createViews()
                ->createTests();

            return $this;
        }

        return false;
    }

    private function CreateBundleDirs($bundle, $prependDir)
    {
        $bundle = str_replace(array('\\', '//'), array('//', '/'), $bundle);

        if(is_dir($prependDir.'/'.$bundle))
            return false;

        $bundleDirs = explode('/', $bundle);
        $createDir = $prependDir;

        foreach($bundleDirs as $bundle)
        {
            $createDir .= '/'.$bundle;

            if(!is_dir($createDir))
            {
                if(!mkdir(str_replace('//','/', $createDir)))
                {
                    echo $this->red('Unable to create directory '.realpath($createDir)), $this->linebreak();
                    return false;
                }
            }
        }

        return $this;
    }

    public function DeleteBundle($bundleName) {

        if ($this->removeDirectory($this->bundleSourceFolder . $bundleName))
        {
            return $this;
        }

        return false;

    }

    public function DeleteAsset($bundleName)
    {
        if(is_dir($this->bundleAssetsFolder .  $bundleName))
        {
            if($this->removeDirectory($this->bundleAssetsFolder . $bundleName))
            {
                return $this;
            }

            return false;
        }

        return false;
    }

    private function ReplaceBackslashes($string)
    {
        return str_replace('\\','/', $string);
    }

    private function createConfig(){

        mkdir($this->bundleFolder . '/Resources');
        mkdir($this->bundleFolder . $this->bundleConfigsFolder );

        $initTemplate = "<?php

Set::Config('".strtoupper($this->name)."', array(
    'Path' => \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') . '".strtoupper($this->name)."'
));";

        $this->createFile($this->bundleFolder . $this->bundleConfigsFolder . "{$this->name}.Config.php", $initTemplate);

        return $this;
    }

    private function createEntity(){


        mkdir($this->bundleFolder . $this->bundleDatabaseFolder . 'Entities');
        mkdir($this->bundleFolder . $this->bundleDatabaseFolder . 'Repositories');

        $initEntity = "<?php

namespace {$this->bundleNamespace}\\Entities;



use \\Application\\Entities\\ApplicationEntity;

use \\{$this->bundleNamespace}\\Interfaces\\{$this->name}EntityInterface;

// This Entity represents {$this->name} table

final class {$this->name}Entity extends ApplicationEntity implements {$this->name}EntityInterface{

}
";

        $initRepository = "<?php

namespace {$this->bundleNamespace}\\Repositories;



use \\Application\\Repositories\\ApplicationRepository;

use \\{$this->bundleNamespace}\\Interfaces\\{$this->name}RepositoryInterface;

// This Repository holds methods to query {$this->name} table

final class {$this->name}Repository extends ApplicationRepository implements {$this->name}RepositoryInterface{

}
              ";

        $this->createFile($this->bundleFolder . $this->bundleDatabaseFolder . "Entities/{$this->name}Entity.php", $initEntity);
        $this->createFile($this->bundleFolder . $this->bundleDatabaseFolder . "Repositories/{$this->name}Repository.php", $initRepository);

        return $this;
    }

    private function createModel()
    {
        mkdir($this->bundleFolder . $this->bundleDatabaseFolder);

        $Model = "<?php

namespace {$this->bundleNamespace}\\Models;



use \\Application\\Models\\ApplicationModel;

use \\{$this->bundleNamespace}\\Interfaces\\{$this->name}ModelInterface;

// Model represents the logic of {$this->name} table with the application

final class {$this->name}Model extends ApplicationModel implements {$this->name}ModelInterface{

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

        $this->createFile($this->bundleFolder . $this->bundleDatabaseFolder . "{$this->name}Model.php", $Model);

        return $this;
    }

    private function createEvent()
    {
        mkdir($this->bundleFolder . $this->bundleEventsFolder);

        $event = "<?php

namespace {$this->bundleNamespace}\\Events;



use \\Application\\Core\\ApplicationEvents;

use \\{$this->bundleNamespace}\\Interfaces\\{$this->name}EventsInterface;

/**
 * Events provide an elegant way of placing decoupled code. Using these are highly recommended.
 */
final class {$this->name}Events extends ApplicationEvents implements {$this->name}EventsInterface{

    /**
     * This method will fire if the EventHandlers Notify method is fires with the event being {$this->name}
     */
    public function {$this->name}Handler()
    {

    }
}";

        $this->createFile($this->bundleFolder . $this->bundleEventsFolder . "{$this->name}Event.php", $event);

        return $this;

    }

    private function createViews(){

        mkdir($this->bundleFolder . $this->bundleViewsFolder);
        mkdir($this->bundleFolder . $this->bundleViewsFolder . 'ControllerViews');

        $initTemplate = "<?=\$this->IncludeTemplate(':Header.html.php', \$params)?>
<?=\$this->setAsset('{$this->name}:{$this->name}.css')?>
";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'Header.html.php', $initTemplate);

        $initTemplate = "<?=\$this->setAsset('{$this->name}:{$this->name}.js')?>
<?=\$this->IncludeTemplate(':Footer.html.php', \$params)?>
";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'Footer.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_Create')?>'>Create new {$this->singular}</a>
    </div>
    <h3>List of all {$this->name}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$table, 'table')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'ControllerViews/list.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>View {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$table, 'table')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'ControllerViews/view.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>Create new {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$form, 'form')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'ControllerViews/create.html.php', $initTemplate);

        $initTemplate = "<div class='wrapper'>
    <div class=''>
        <a href='<?=\$this->setRoute('{$this->name}_List')?>'>View All {$this->name}</a>
    </div>
    <h3>Edit {$this->singular}</h3>
    <div class='widget'>
        <?=\$this->htmlgen->Output(\$form, 'form')?>
    </div>
</div>";

        $this->createFile($this->bundleFolder . $this->bundleViewsFolder . 'ControllerViews/edit.html.php', $initTemplate);

        return $this;
    }

    private function createInterface(){

        // Create all directories
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder);
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder . '/Controllers');
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder . '/Models');
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder . '/Entities');
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder . '/Repositories');
        mkdir($this->bundleFolder . $this->bundleInterfacesFolder . '/Events');

        $ControllerInterface = "<?php

namespace {$this->bundleNamespace}\\Interfaces;


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
     * @return type Description
     *
     * @example path description
     *
     */
    public function listAction();

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

        $RepositoryInterface = "<?php

namespace {$this->bundleNamespace}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}RepositoryInterface {

}
";

        $EntityInterface = "<?php

namespace {$this->bundleNamespace}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}EntityInterface {

}
";

        $ModelInterface = "<?php

namespace {$this->bundleNamespace}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}ModelInterface {

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function Create{$this->singular}();

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function Update{$this->singular}();

    /**
     *
     * @author <Above>
     *
     * @return type Description
     *
     * @example path description
     *
     */
    public function Delete{$this->singular}();
}
";

        $EventsInterface = "<?php

namespace {$this->bundleNamespace}\\Interfaces;



/**
 *
 * @group groupName
 * @author Abc <Abc@example.com>
 *
 */
interface {$this->name}EventsInterface {

}
";

        $this->createFile($this->bundleFolder . $this->bundleInterfacesFolder . "Controllers/{$this->name}Controller.Interface.php", $ControllerInterface);
        $this->createFile($this->bundleFolder . $this->bundleInterfacesFolder . "Repositories/{$this->name}Repository.Interface.php", $RepositoryInterface);
        $this->createFile($this->bundleFolder . $this->bundleInterfacesFolder . "Entities/{$this->name}Entity.Interface.php", $EntityInterface);
        $this->createFile($this->bundleFolder . $this->bundleInterfacesFolder . "Models/{$this->name}Model.Interface.php", $ModelInterface);
        $this->createFile($this->bundleFolder . $this->bundleInterfacesFolder . "Events/{$this->name}Events.Interface.php", $EventsInterface);

        return $this;
    }

    private function createController(){

        mkdir($this->bundleFolder . $this->bundleControllersFolder);

        $initController = "<?php

namespace {$this->bundleNamespace}\\Controllers;



use \\{$this->bundleNamespace}\\Entities\\{$this->name}Entity;
use \\{$this->bundleNamespace}\\Models\\{$this->name}Model;
use \\{$this->bundleNamespace}\\Interfaces\\{$this->name}ControllerInterface;

// Controller is responsible for the interactions between a model and a template

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
        \${$this->name}Model = new {$this->name}Model();
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
        \${$this->name}Model = new {$this->name}Model();
        \${$this->name}Model->SetEntity('{$this->name}:{$this->name}');

        if(\$this->GetRequestManager()->isPost('submit'))
        {
            if (!\${$this->name}Model->SetEntity('{$this->name}:{$this->name}', \$this->GetRequestManager()->PostParams()))
            {
                \$this->setError('Empty data passed');
            }

            if(\${$this->name}Model->Create{$this->singular}())
            {
                \$this->SetFlash(array('Success' => 'Create successful.'));
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
        \${$this->name}Model = new {$this->name}Model();
        \${$this->name}Model->SetEntity('{$this->name}:{$this->name}');

        if(\$this->GetRequestManager()->isPost('submit'))
        {
            \${$this->name}Model->SetEntity('{$this->name}:{$this->name}', \$this->GetRequestManager()->PostParams());

            if(\${$this->name}Model->Update{$this->singular}())
            {
                \$this->SetFlash(array('Success' => 'Update successful.'));
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
            \${$this->name}Model = new {$this->name}Model(\${$this->name}Entity);

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

        $this->createFile($this->bundleFolder . $this->bundleControllersFolder . "{$this->name}Controller.php", $initController);

        $initController = "<?php

namespace {$this->bundleNamespace}\\Controllers;



use \\Application\\Controllers\\ApplicationController;


// Use this class to inherit methods used in all or some of your {$this->name} bundle controllers
// {$this->name} bundle created at: " . date('l, d F, Y') . "

class {$this->name}BundleController extends ApplicationController{

}

";

        $this->createFile($this->bundleFolder . $this->bundleControllersFolder . "{$this->name}BundleController.php", $initController);

        return $this;
    }

    private function createRoutes(){

        mkdir($this->bundleFolder . $this->bundleRoutesFolder);

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
    'Pattern' => '/{$this->name}/View/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));

Set::Route('{$this->name}_Create', array(

    'Controller' => '{$this->name}:{$this->name}:create',
    'Pattern' => '/{$this->name}/Create/'
));

Set::Route('{$this->name}_Edit', array(

    'Controller' => '{$this->name}:{$this->name}:edit',
    'Pattern' => '/{$this->name}/Edit/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));

Set::Route('{$this->name}_Delete', array(

    'Controller' => '{$this->name}:{$this->name}:delete',
    'Pattern' => '/{$this->name}/Delete/{id}/',
    'Requirements' => array(
        'id' => '/^\d+$/'
    )
));
";

        $this->createFile($this->bundleFolder . $this->bundleRoutesFolder . "{$this->name}.Routes.php", $initRoute);

        return $this;
    }

    private function createTests(){

        mkdir($this->bundleFolder . $this->bundleTestsFolder);
        mkdir($this->bundleFolder . $this->bundleTestsFolder . 'Config');

        $initTests = "<?php


Set::Config('{$this->name}Testing', array());";

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Config/{$this->name}.Test.Config.php", $initTests);

        mkdir($this->bundleFolder . '/Tests/Scenarios');

        $initTests = "<?php

namespace {$this->bundleNamespace}\\Tests;

require_once __DIR__ . '/../Config/{$this->name}.Test.Config.php';



use Application\\Console\\WebTestCase;


class Test{$this -> name}Controller extends WebTestCase
{
    public function testIndexAction()
    {
        self::\$testClass = new {$this->bundleNamespace}\\Controllers\\{$this->name}Controller();

        \$method = 'IndexAction';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'string'));
    }
}";

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Scenarios/{$this->name}Controller.Test.php", $initTests);

        $initTests = "<?php

namespace {$this->bundleNamespace}\\Tests;

require_once __DIR__ . '/../Config/{$this->name}.Test.Config.php';



use Application\\Console\\BaseTestingRoutine;


class Test{$this -> name}Entity extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::\$testClass = new \\Bundles\\{$this->name}\\Entities\\{$this->name}Entity();

        \$method = '';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'string'));
    }
}";

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Scenarios/{$this->name}Entity.Test.php", $initTests);

        $initTests = "<?php

namespace {$this->bundleNamespace}\\Tests;

require_once __DIR__ . '/../Config/{$this->name}.Test.Config.php';


use Application\\Console\\BaseTestingRoutine;


class Test{$this -> name}Repository extends BaseTestingRoutine
{
    public function testExampleMethod()
    {
        self::\$testClass = new \\Bundles\\{$this->name}\\Repositories\\{$this->name}Repository();

        \$method = '';

        //Checks if the returned value of this function is an integer
        \$this->AssertTrue(\$method, array('case' => 'array'));
    }
}";

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Scenarios/{$this->name}Repository.Test.php", $initTests);

        $initTests = "<?php

namespace {$this->bundleNamespace}\\Tests;

require_once __DIR__ . '/../Config/{$this->name}.Test.Config.php';



use Application\\Console\\TemplateTestCase;


class Test{$this -> name}Templates extends TemplateTestCase
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

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Scenarios/{$this->name}Templates.Test.php", $initTests);

        $initTests = "<?php

namespace {$this->bundleNamespace}\\Tests;

require_once __DIR__ . '/../Config/{$this->name}.Test.Config.php';



use Application\\Console\\BaseTestingRoutine;


class Test{$this -> name}Model extends BaseTestingRoutine
{
    public function testModelCreate{$this->singular}Method()
    {

    }

    public function testModelUpdate{$this->singular}Method()
    {

    }

    public function testModelDelete{$this->singular}Method()
    {

    }
}";

        $this->createFile($this->bundleFolder . $this->bundleTestsFolder . "Scenarios/{$this->name}Model.Test.php", $initTests);

        return $this;
    }

    public function CreateAssets(){

        if($this->CreateBundleDirs($this->bundleDirInAssets, $this->bundleAssetsFolder))
        {
            $fullPath = $this->bundleAssetsFolder.$this->bundleDirInAssets;

            mkdir($fullPath . '/Images');
            mkdir($fullPath . '/JS');
            mkdir($fullPath . '/CSS');

            $initJs = "/* Javascript for {$this->name} Bundle */

jQuery(document).ready(function(){

});

";

            $this->createFile($fullPath . "/JS/{$this->name}.js", $initJs);

            $initCss= "/* Stylesheet for {$this->name} Bundle */

root{
    font-size: 12px;
    font-family: verdana;
    color: black;
}";

            $this->createFile($fullPath . "/CSS/{$this->name}.css", $initCss);

            return $this;
        }
        else
            echo $this->linebreak (2) , $this->red ('Unable to create assets in folder: '.$this->bundleAssetsFolder);
    }
}