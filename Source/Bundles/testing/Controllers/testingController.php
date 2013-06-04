<?php

namespace Application\Bundles\testing\Controllers;



use \Application\Bundles\testing\Entities\testingEntity;
use \Application\Bundles\testing\Repositories\testingRepository;

use \Application\Components\HTMLGenerator\HTMLGenerator;

use Application\Bundles\testing\Interfaces\testingControllerInterface;


final class testingController extends testingBundleController implements testingControllerInterface{

      public
            $htmlgen;

      public function indexAction(){

              $this->forwardToController("testing_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All testing";

              //Used by the HTMLGenerator in the list view.
              $params['table'] = array(

                'class' => 'paginate',
                'title' => 'Dataset',
                'tbody' => $this->GetRepository("testing:testing")->GetAll(array('order by' => 'id desc')),
                'ignoreFields' => array(),
                'actions' => array(

                    'Edit' => array(

                        'route' => 'testing_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'testing__id',
                    ),

                    'View' => array(

                        'route' => 'testing_View',
                        'routeParam' => 'id',
                        'dataParam' => 'testing__id',
                    ),

                    'Delete' => array(

                        'message' => 'Are you sure you want to delete this record?',
                        'class' => 'remove',
                        'route' => 'testing_Delete',
                        'routeParam' => 'id',
                        'dataParam' => 'testing__id',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

              $this->Render("testing:list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View testing";

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $this->GetEntity("testing:testing")->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'testing_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'testing__id',
                    ),
                  ),
              );

              $this->htmlgen = new HTMLGenerator();

              $this->Render("testing:view.html.php", $params);

      }

      public function createAction(){

            if($this->GetRequest->isPost("submit")){

              if($this->GetEntity("testingBundle:testing")->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("testing_List");

            }

            $params["PageTitle"] = "Create New testing";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('testing_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),
                'table' => $this->GetEntity("testing:testing")->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = new HTMLGenerator();

            $this->Render("testing:create.html.php", $params);

      }

      public function editAction($id){

            if($this->GetRequest()->isPost("submit")){

              if($this->getEntity("testingBundle:testing")->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("testing_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('testing_Edit', array('id' => $id)),
                'table' => $this->GetEntity('testing:testing')->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit testing";

            $this->htmlgen = new HTMLGenerator();

            $this->Render("testing:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->isAjax()){

              $testing = $this->getEntity("testing:testing");

              if($testing->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }
}
              