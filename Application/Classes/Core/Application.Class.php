<?php

namespace Application\Core;



class Application extends Template{

    private
            $Router,
            $Request,
            $Response;
    public
            $User;

    public function __construct() {

        $this->Request = $this->GetCoreObject('Request');
        $this->Router = $this->GetCoreObject('Router');
        $this->Response = $this ->GetCoreObject('Response');

        if(\Get::Config('Application.Session.Enabled'))
        {
            $session = $this->GetCoreObject('Session');

            call_user_func_array(array($session, 'Start'), \Get::Config('Application.Session.Secure', 'Application.Session.HttpOnly'));

            // Check for login interval expiration and authorized page view
            if($session->IsSessionKeySet('login_expires'))
            {
                if(time() > $session->Get('login_expires'))
                {
                    $session->Destroy()->Start();

                    $this
                        ->setError(array('Logged Out' => 'Your session has expired, please login again.'))
                            ->forwardTo(\Get::Config('Auth.Login.LoginRoute'));
                }
            }
            else
            {
                if(!$session->IsSessionKeySet('login_expires') AND $this->checkExceptionRoutes() AND !$session->IsSessionKeySet('route_error'))
                {
                    $session->Set('AccessedRoute', $this->Router->getRouteFromPattern());

                    $this
                        ->setError(array('Access Denied' => 'You need to login to access this page.'))
                            ->forwardTo(\Get::Config('Auth.Login.LoginRoute'));
                }

            }

            // Populate User object with user defined method
            if(\Get::Config('Auth.Login.EntityRepository') AND $session->IsSessionKeySet('login_time'))
            {
                $auth = new Auth();

                $this->User = $auth->GetUser();

                $tableColumn = \Get::Config('Auth.DBTable.AuthColumnName');

                // Prevent Session Hijacking
                if(isset($this->User->$tableColumn))
                {
                    $browser = $this->GetBrowserAgent();

                    $db = new Database();

                    $password = $db->Table(\Get::Config('Auth.DBTable.AuthTableName'), array('password'))->GetOneRecordBy(array(\Get::Config('Auth.DBTable.AuthColumnName') => $this->User->$tableColumn));

                    $login_check = hash(\Get::Config('Auth.Security.PasswordEncryption'), $password->password.$browser);

                    if($login_check != $session->Get('login_string'))
                    {
                        $session->Destroy()->Start();
                    }
                }
            }
        }
    }

    /**
     *
     * @param type $roleId, provide the role id to match against.
     * @return boolean returns true on accable, redirects to APPLICATION BASE ROUTE NAME on false.<br />
     * You can use this function of you have a user role id set in your application and database, to make this work, you need to have role id assigned<br />
     * in user populate function by the name RoleId. The roles should be ascending where the higher the role id the lesser the permissions.
     */
    public function checkIfAccessableBy($roleId = 1){

        if($this->User->RoleId <= $roleId){

            return true;
        }
        else {

            $this->setError('You need more previliges to access this page.')->forwardTo(APPLICATION_BASE_ROUTE_NAME);
        }

    }

    /**
     *
     * @param type $roleId provide the role id to match against.
     * @return boolean returns true on success, false on failure.
     * You can use this function of you have a user role id set in your application and database, to make this work, you need to have role id assigned<br />
     * in user populate function by the name RoleId. The roles should be ascending where the higher the role id the lesser the permissions. Useful in templating.
     */
    public function userRoleIs($roleId = 1){

        if($this->User->RoleId <= $roleId){

            return true;
        }
        else {

            return false;
        }
    }

    protected function PageUnderDevelopment()
    {
        if($this->Variable(\Get::Config('Application.Environment.UnderDevelopmentPage.ExemptIPs'))->Search(getenv('REMOTE_ADDR')) === false)
        {
            echo 'This page is under development, please check back later';
            exit;
        }
    }
}