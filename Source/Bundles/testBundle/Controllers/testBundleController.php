<?php

namespace Application\Bundles\TestBundle\Controllers;



use \Application\Core\Controllers\ApplicationController;

use \Application\Bundles\TestBundle\Entities\testBundleEntity;
use \Application\Bundles\TestBundle\Repositories\testBundleRepository;

use \Application\Components\HTMLGenerator\HTMLGenerator;


class testBundleController extends ApplicationController{

      public function indexAction(){

              $this->forwardToController("testBundle_List");

      }

      public function listAction(){

              $params["PageTitle"] = "All testBundle";

              //Used by the HTMLGenerator in the list view.
              $params['table'] = array(

                'class' => 'paginate',
                'title' => 'Dataset',
                'tbody' => $this->GetRepository('TestBundle:testBundle')->GetAll(array('order by' => 'id desc')),
                'ignoreFields' => array('Users__password'),
                'actions' => array(

                    'Edit' => array(

                        'route' => 'testBundle_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'testBundle__id',
                    ),

                    'View' => array(

                        'route' => 'testBundle_View',
                        'routeParam' => 'id',
                        'dataParam' => 'testBundle__id',
                    ),

                    'Delete' => array(

                        'message' => 'Are you sure you want to delete this record?',
                        'class' => 'remove',
                        'route' => 'testBundle_Delete',
                        'routeParam' => 'id',
                        'dataParam' => 'testBundle__id',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:testBundle:list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View testBundle";

              $testBundle = new testBundleEntity();

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $testBundle->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'testBundle_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'testBundle__id',
                    ),
                  ),
              );

              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:testBundle:view.html.php", $params);

      }

      public function createAction(){

          $testBundle = new testBundleEntity();

            if($this->GetRequest()->isPost("submit")){

              if($testBundle->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("testBundle_List");

            }

            $params["PageTitle"] = "Create New testBundle";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('testBundle_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),

                'table' => $testBundle->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:testBundle:create.html.php", $params);

      }

      public function editAction($id){

            $testBundle = new testBundleEntity($id);

            if($this->GetRequest()->isPost("submit")){

              if($testBundle->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("testBundle_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('testBundle_Edit', array('id' => $id)),
                'table' => $testBundle->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit testBundle";

            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:testBundle:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->GetRequest()->isAjax()){

              $testBundle = new testBundle();

              if($testBundle->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }
}
