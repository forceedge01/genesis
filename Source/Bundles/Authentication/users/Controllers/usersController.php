<?php

namespace Bundles\Authentication\users\Controllers;

use \Bundles\users\Entities\usersEntity;
use \Bundles\users\Models\usersModel;
use \Bundles\users\Interfaces\usersControllerInterface;

// Controller is responsible for the interactions between a model and a template

final class usersController extends usersBundleController implements usersControllerInterface {

    public function indexAction()
    {
        $this->forwardToController("users_List");
    }

    public function loginAction()
    {
        $this->GetComponent('HTMLGenerator');

        $this->Notify('logRegister');

        $this->IncludeHeaderAndFooter();

        $this->Render('users:users:login.html.php', 'Login');
    }

    public function loginAuthAction()
    {
        if ($this->GetRequestManager()->IsPost('login'))
        {
            $auth = $this->GetComponent('Auth');
            $auth->SetPostParams();

            if ($auth->authenticateUser('Invalid username or password entered'))
            {
                $auth->ForwardToLoggedInPage();
            }

            $auth->ForwardToLoginPage();
        }
        else
        {
            $this->setError('Unable to login')->ForwardTo('users_login');
        }
    }

    public function logoutAction()
    {
        $this->GetComponent('Auth')->Logout('You have been logged out.');
    }

    public function listAction()
    {
        //Used by the HTMLGenerator in the list view.
        $params['table'] = array(
            'class' => 'paginate',
            'title' => 'Dataset',
            'tbody' => $this
                        ->GetRepository("users:users")
                            ->GetAll(array('order by' => 'id desc')),
            'ignoreFields' => array('users__password', 'users__salt'),
        );

        //This will be used in the template to generate the above declared table.
        $this->htmlgen = $this->GetComponent('HTMLGenerator');

        $this->IncludeHeaderAndFooter();
        $this->Render("users:users:list.html.php", 'All users', $params);
    }

    public function viewAction($id)
    {
        $usersModel = new usersModel();
        $usersModel->SetEntity('users:users');

        $params["table"] = array(
            'title' => 'View',
            'class' => 'paginate',
            'tbody' => $usersModel
                        ->GetEntityObject()
                            ->Get($id),
            'actions' => array(
                'Edit' => array(
                    'route' => 'users_Edit',
                    'routeParam' => 'id',
                    'dataParam' => 'users__id',
                ),
            ),
        );

        $this->htmlgen = $this->GetComponent('HTMLGenerator');

        $this->Render("users:users:view.html.php", "View User {$id}", $params);
    }

    public function createAction()
    {
        $usersModel = new usersModel();
        $usersModel->SetEntity('users:users');

        if ($this->GetRequestManager()->isPost("submit"))
        {
            if (!$usersModel->SetEntity('users:users', $this->GetRequestManager()->PostParams()))
            {
                $this->SetError('Empty data passed');
            }

            if ($usersModel->CreateUser())
            {
                $this->SetFlash(array("Success" => "Create successful."));
            }
            else
            {
                $this->SetError(array("Failure" => "Failed to create."));
            }

            $this->forwardTo("users_List");
        }

        $params['form'] = array(
            'class' => 'form',
            'action' => $this->setRoute('users_Create'),
            'title' => 'Random Title',
            'table' => $usersModel
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
        $this->htmlgen = $this->GetComponent('HTMLGenerator');

        $this->Render("users:users:create.html.php", 'Create New users', $params);
    }

    public function editAction($id) {

        $usersModel = new usersModel();

        $usersModel->SetEntity('users:users');

        if ($this->Request()->isPost("submit"))
        {
            $usersModel->SetEntity('users:users', $this->GetRequestManager()->PostParams());

            if ($usersModel->UpdateUser())
            {
                $this->SetFlash(array("Success" => "Update successful."));
            }
            else
            {
                $this->SetError(array("Failure" => "Failed to update."));
            }

            $this->forwardTo("users_List");
        }

        $params['form'] = array(
            'title' => 'Edit',
            'action' => $this->setRoute('users_Edit', array('id' => $id)),
            'table' => $usersModel
                        ->GetEntityObject()
                            ->Get($id),
            'submission' => array(
                'submit' => array(
                    'value' => 'Save changes',
                    'name' => 'submit'
                ),
            )
        );

        $this->htmlgen = $this->GetComponent('HTMLGenerator');

        $this->Render('users:users:edit.html.php', 'Edit users', $params);
    }

    /**
     *
     * @param int $id the id to delete from the database
     * By default is ajax controlled.
     *
     */
    public function deleteAction($id) {

        if ($this->GetRequestManager()->isAjax())
        {
            $usersEntity = new usersEntity($id);
            $usersModel = new usersModel($usersEntity);

            if ($usersModel->DeleteUser())
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
