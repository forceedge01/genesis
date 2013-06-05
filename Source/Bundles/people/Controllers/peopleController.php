<?php

namespace Application\Bundles\people\Controllers;



use \Application\Bundles\people\Entities\peopleEntity;
use \Application\Bundles\people\Repositories\peopleRepository;

use \Application\Components\HTMLGenerator\HTMLGenerator;

use Application\Bundles\people\Interfaces\peopleControllerInterface;


final class peopleController extends peopleBundleController implements peopleControllerInterface{

      public
            $htmlgen;

      public function indexAction(){

              $this->forwardToController("people_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All people";

              //Used by the HTMLGenerator in the list view.
              $params['table'] = array(

                'class' => 'paginate',
                'title' => 'Dataset',
                'tbody' => $this->GetRepository("people:Users")->find(3),
                'ignoreFields' => array(),
                'actions' => array(

                    'Edit' => array(

                        'route' => 'people_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'Users__id',
                    ),

                    'View' => array(

                        'route' => 'people_View',
                        'routeParam' => 'id',
                        'dataParam' => 'Users__id',
                    ),

                    'Delete' => array(

                        'message' => 'Are you sure you want to delete this record?',
                        'class' => 'remove',
                        'route' => 'people_Delete',
                        'routeParam' => 'id',
                        'dataParam' => 'Users__id',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = $this ->GetComponent('HTMLGenerator');

              $this->Render("people:list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View people";

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $this->GetEntity("people:Users")->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'people_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'people__id',
                    ),
                  ),
              );

              $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

              $this->Render("people:view.html.php", $params);

      }

      public function createAction(){

            if($this->GetRequest() ->isPost("submit")){

              if($this->GetEntity("people:Users")->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("people_List");

            }

            $params["PageTitle"] = "Create New people";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('people_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),
                'table' => $this->GetEntity("people:Users")->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

            $this->Render("people:create.html.php", $params);

      }

      public function editAction($id){

            if($this->GetRequest()->isPost("submit")){

              if($people = $this->getEntity("peopleBundle:people")->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("people_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('people_Edit', array('id' => $id)),
                'table' => $this->GetEntity('people:Users')->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit people";

            $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

            $this->Render("people:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->isAjax()){

              $people = $this->getEntity("people:Users");

              if($people->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }

      public function add2($var1, $var2)
      {
          return $var1+$var2;
      }
}
