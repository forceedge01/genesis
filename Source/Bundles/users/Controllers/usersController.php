<?php

namespace Bundles\users\Controllers;



use \Bundles\users\Entities\usersEntity;
use \Bundles\users\Repositories\usersRepository;

use \Application\Components\HTMLGenerator\HTMLGenerator;

use \Bundles\users\Interfaces\usersControllerInterface;


final class usersController extends usersBundleController implements usersControllerInterface{

      public
            $htmlgen;

      public function indexAction(){

              $this->forwardToController("users_List");
      }

      public function loginAction()
      {
          $this->Render('users:login.html.php', 'Login');
      }

      public function loginAuthAction()
      {

          if($this->GetRequestManager()->IsPost('login'))
          {
              $auth = new \Application\Core\Auth();

              if($auth->authenticateUser('Invalid username or password entered'))
              {
                  $auth->forwardToLoggedInPage();
              }

              $auth->forwardToLoginPage();
          }
          else
          {
              $this->setError('Unable to login')->forwardTo('users_login');
          }
      }

      public function logoutAction()
      {
          $auth = new \Application\Core\Auth();
          $auth->logout('You have been logged out.');
      }

      public function listAction()
      {
          $params["PageTitle"] = "All users";

            //Used by the HTMLGenerator in the list view.
            $params['table'] = array(

              'class' => 'paginate',
              'title' => 'Dataset',
              'tbody' => $this->GetRepository("users:users")->GetAll(array('order by' => 'id desc')),
              'ignoreFields' => array('users__password', 'users__salt'),

            );

            //This will be used in the template to generate the above declared table.
            $this->htmlgen = $this ->GetComponent('HTMLGenerator');

            $this->Render("users:list.html.php", 'All users', $params);

      }

      public function viewAction($id){

              $params["PageTitle"] = "View users";

              $params["table"] = array(

                  'title' => 'View',
                  'class' => 'paginate',
                  'tbody' => $this->GetEntity("users:users")->Get($id),
                  'actions' => array(

                      'Edit' => array(

                        'route' => 'users_Edit',
                        'routeParam' => 'id',
                        'dataParam' => 'users__id',
                    ),
                  ),
              );

              $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

              $this->Render("users:view.html.php", "View User {$id}", $params);

      }

      public function createAction(){

            if($this->Request() ->isPost("submit")){

              if($this->GetEntity("usersBundle:users")->Save())
                  $this->setFlash(array("Success" => "Create successful."));
              else
                  $this->setError(array("Failure" => "Failed to create."));

              $this->forwardTo("users_List");

            }

            $params["PageTitle"] = "Create New users";

            $params['form'] = array(

                'class' => 'form',
                'action' => $this->setRoute('users_Create'),
                'title' => 'Random Title',
                'inputs' => array(

                    'text' => array(

                        'label' => 'Name',
                        'name' => 'Name',
                        'value' => 'Enter your name',
                    )
                ),
                'table' => $this->GetEntity("users:users")->GetFormFields(),

                'submission' => array(

                    'submit' => array(

                        'value' => 'Create new record',
                        'name' => 'submit'
                    ),
               ),

            );

            //This will be used in the template to generate the above declared form.
            $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

            $this->Render("users:create.html.php", $params);

      }

      public function editAction($id){

            if($this->Request()->isPost("submit")){

              if($users = $this->getEntity("usersBundle:users")->Save())
                  $this->setFlash(array("Success" => "Update successful."));
              else
                  $this->setError(array("Failure" => "Failed to update."));

              $this->forwardTo("users_List");

            }

            $params["form"] = array(

                'title' => 'Edit',
                'action' => $this->setRoute('users_Edit', array('id' => $id)),
                'table' => $this->GetEntity('users:users')->Get($id),
                'submission' => array(

                    'submit' => array(

                        'value' => 'Save changes',
                        'name' => 'submit'
                    ),
                )

            );

            $params["PageTitle"] = "Edit users";

            $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

            $this->Render("users:edit.html.php", $params);

      }

      /**
       *
       * @param int $id the id to delete from the database
       * By default is ajax controlled.
       *
       */
      public function deleteAction($id){

            if($this->Request()->isAjax()){

              $usersEntity = new \Bundles\users\Entities\usersEntity(2);
              $usersModel = new \Bundles\users\Models\usersModel($usersEntity);
              $usersModel->DeleteUser();

              $users = $this->getEntity("users:users");

              if($users->delete($id))
                  echo 'success:Delete was successful';
              else
                  echo 'error:Delete was unsuccessful';
            }
      }
}
