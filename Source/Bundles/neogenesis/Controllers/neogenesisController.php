<?php

namespace Bundles\neogenesis\Controllers;



use \Bundles\neogenesis\Entities\neogenesisEntity;
use \Bundles\neogenesis\Repositories\neogenesisRepository;

use \Application\Components\HTMLGenerator\HTMLGenerator;


class neogenesisController extends neogenesisBundleController{

      public
            $htmlgen;

      public function indexAction(){

              $this->forwardToController("neogenesis_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All neogenesis";

              //Used by the HTMLGenerator in the list view.
              $params['table'] = array(

                'class' => 'paginate',
                'title' => 'Dataset',
                'tbody' => $this->GetRepository("neogenesis:neogenesis")->GetAll(array('order by' => 'id desc')),
                'ignoreFields' => array(),
                'actions' => array(

                    'Edit' => array(

                        'route' => 'neogenesis_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'neogenesis__id',
                    ),

                    'View' => array(

                        'route' => 'neogenesis_View',
                        'routeParam' => 'id',
                        'dataParam' => 'neogenesis__id',
                    ),

                    'Delete' => array(

                        'message' => 'Are you sure you want to delete this record?',
                        'class' => 'remove',
                        'route' => 'neogenesis_Delete',
                        'routeParam' => 'id',
                        'dataParam' => 'neogenesis__id',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:neogenesis:list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View neogenesis";

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $this->GetEntity("neogenesis:neogenesis")->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'neogenesis_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'neogenesis__id',
                    ),
                  ),
              );

              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:neogenesis:view.html.php", $params);

      }

      public function createAction(){

            if($this->GetRequest->isPost("submit")){

              if($this->GetEntity("neogenesisBundle:neogenesis")->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("neogenesis_List");

            }

            $params["PageTitle"] = "Create New neogenesis";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('neogenesis_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),
                'table' => $this->GetEntity("neogenesis:neogenesis")->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:neogenesis:create.html.php", $params);

      }

      public function editAction($id){

            if($this->GetRequest()->isPost("submit")){

              if($neogenesis = $this->getEntity("neogenesisBundle:neogenesis")->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("neogenesis_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('neogenesis_Edit', array('id' => $id)),
                'table' => $this->GetEntity('neogenesis:neogenesis')->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit neogenesis";

            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:neogenesis:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->isAjax()){

              $neogenesis = $this->getEntity("neogenesis:neogenesis");

              if($neogenesis->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }
}
