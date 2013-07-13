<?php

namespace Bundles\Clients\Controllers;



use \Bundles\Clients\Entities\ClientsEntity;
use \Bundles\Clients\Models\ClientsModel;
use \Bundles\Clients\Interfaces\ClientsControllerInterface;

// Controller is responsible for the interactions between a model and a template

final class ClientsController extends ClientsBundleController implements ClientsControllerInterface{

    public
          $htmlgen;

    public function indexAction()
    {
        $this->forwardToController('Clients_List');
    }

    public function listAction()
    {
        //Used by the HTMLGenerator in the list view.
        $params['table'] = array(

          'class' => 'paginate',
          'title' => 'Dataset',
          'tbody' => $this
                          ->GetRepository('Clients:Clients')
                              ->GetAll(array('order by' => 'id desc')),
          'ignoreFields' => array(),
          'actions' => array(

              'Edit' => array(

                  'route' => 'Clients_Edit',
                  'routeParam' => 'id',
                  'dataParam' => 'Clients__id',
              ),

              'View' => array(

                  'route' => 'Clients_View',
                  'routeParam' => 'id',
                  'dataParam' => 'Clients__id',
              ),

              'Delete' => array(

                  'message' => 'Are you sure you want to delete this record?',
                  'class' => 'remove',
                  'route' => 'Clients_Delete',
                  'routeParam' => 'id',
                  'dataParam' => 'Clients__id',
              ),
          )

        );

        //This will be used in the template to generate the above declared table.
        $this->htmlgen = $this ->GetComponent('HTMLGenerator');

        $this->Render('Clients:list.html.php', 'List Clients', $params);

    }

    public function viewAction($id)
    {
        $ClientsModel = new ClientsModel();
        $ClientsModel->SetEntity('Clients:Clients');

        $params['table'] = array(

            'title' => 'View',
            'class' => 'paginate',
            'tbody' => $ClientsModel
                        ->GetEntityObject()
                            ->Get($id),
            'actions' => array(

                'Edit' => array(

                  'route' => 'Clients_Edit',
                  'routeParam' => 'id',
                  'dataParam' => 'Clients__id',
              ),
            ),
        );

        $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

        $this->Render('Clients:view.html.php', 'View Client', $params);
    }

    public function createAction()
    {
        $ClientsModel = new ClientsModel();
        $ClientsModel->SetEntity('Clients:Clients');

        if($this->GetRequestManager()->isPost('submit'))
        {
            if (!$ClientsModel->SetEntity('Clients:Clients', $this->GetRequestManager()->PostParams()))
            {
                $this->setError('Empty data passed');
            }

            if($ClientsModel->CreateClient())
            {
                $this->SetFlash(array('Success' => 'Create successful.'));
            }
            else
            {
                $this->setError(array('Failure' => 'Failed to create.'));
            }

            $this->forwardTo('Clients_List');
        }

        $params['form'] = array(

            'class' => 'form',
            'action' => $this->setRoute('Clients_Create'),
            'title' => 'Random Title',
            'inputs' => array(

                'text' => array(

                    'label' => 'Name',
                    'name' => 'Name',
                    'value' => 'Enter your name',
                )
            ),
            'table' => $ClientsModel
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
        $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

        $this->Render('Clients:create.html.php', 'Create new Client', $params);
    }

    public function editAction($id)
    {
        $ClientsModel = new ClientsModel();
        $ClientsModel->SetEntity('Clients:Clients');

        if($this->GetRequestManager()->isPost('submit'))
        {
            $ClientsModel->SetEntity('Clients:Clients', $this->GetRequestManager()->PostParams());

            if($ClientsModel->UpdateClient())
            {
                $this->SetFlash(array('Success' => 'Update successful.'));
            }
            else
            {
                $this->setError(array('Failure' => 'Failed to update.'));
            }

            $this->forwardTo('Clients_List');
        }

        $params['form'] = array(

            'title' => 'Edit',
            'action' => $this->setRoute('Clients_Edit', array('id' => $id)),
            'table' => $ClientsModel
                        ->GetEntityObject()
                            ->Get($id),
            'submission' => array(

                'submit' => array(

                    'value' => 'Save changes',
                    'name' => 'submit'
                ),
            )

        );

        $this->htmlgen = $this ->GetComponent('HTMLGenerator') ;

        $this->Render('Clients:edit.html.php', 'Edit Client', $params);
    }

    /**
     *
     * @param int $id the id to delete from the database
     * By default is ajax controlled.
     *
     */
    public function deleteAction($id)
    {
        if($this->GetRequestManager()->isAjax())
        {
            $ClientsEntity = new ClientsEntity($id);
            $ClientsModel = new ClientsModel($ClientsEntity);

            if($ClientsModel->DeleteClient())
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

