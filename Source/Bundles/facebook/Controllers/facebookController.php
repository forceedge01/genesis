<?php

class facebookController extends facebookBundleController{

      public function indexAction(){

              $this->forwardToController("facebook_List");
      }

      public function listAction(){

              $params["PageTitle"] = "All facebook";

              $facebook = new facebook();

              //Used by the HTMLGenerator in the list view.
              $params['table'] = array(

                'class' => 'paginate',
                'title' => 'Dataset',
                'tbody' => $facebook->GetAll(array('order by' => 'id desc')),
                'ignoreFields' => array(),
                'actions' => array(

                    'Edit' => array(

                        'route' => 'facebook_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'facebook__id',
                    ),

                    'View' => array(

                        'route' => 'facebook_View',
                        'routeParam' => 'id',
                        'dataParam' => 'facebook__id',
                    ),

                    'Delete' => array(

                        'message' => 'Are you sure you want to delete this record?',
                        'class' => 'remove',
                        'route' => 'facebook_Delete',
                        'routeParam' => 'id',
                        'dataParam' => 'facebook__id',
                    ),
                )

              );

              //This will be used in the template to generate the above declared table.
              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:facebook:list.html.php", $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View facebook";

              $facebook = new facebook();

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $facebook->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'facebook_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'facebook__id',
                    ),
                  ),
              );

              $this->htmlgen = new HTMLGenerator();

              $this->Render("Bundle:facebook:view.html.php", $params);

      }

      public function createAction(){

            if($this->isPost("submit")){

              $facebook = new facebook();

              if($facebook->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("facebook_List");

            }

            $params["PageTitle"] = "Create New facebook";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('facebook_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),
                'table' => $facebook->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:facebook:create.html.php", $params);

      }

      public function editAction($id){

            $facebook = new facebook($id);

            if($this->isPost("submit")){

              if($facebook->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("facebook_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('facebook_Edit', array('id' => $id)),
                'table' => $facebook->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit testBundle";

            $this->htmlgen = new HTMLGenerator();

            $this->Render("Bundle:facebook:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->isAjax()){

              $facebook = new facebook();

              if($facebook->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }
}
              