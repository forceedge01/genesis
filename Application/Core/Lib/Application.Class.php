<?php

namespace Application\Core;



abstract class Application extends Template implements Interfaces\Application{

    private
            $Router,
            $Request,
            $Response;
    public
            $User;

    public function __construct() {

        $this->BeforeApplicationHook();

        $this->Request = $this->GetCoreObject('Request');
        $this->Router = $this->GetCoreObject('Router');
        $this->Response = $this ->GetCoreObject('Response');

        if(\Get::Config('Application.Session.Enabled'))
        {
            $session = $this->GetCoreObject('Session');

            if(\Get::Config('Application.Session.Secure.HttpsSecure') or \Get::Config('Application.Session.Secure.HttpOnly'))
            {
                call_user_func_array(array($session, 'StartSecure'), \Get::Config('Application.Session.Secure.HttpsSecure', 'Application.Session.Secure.HttpOnly'));
            }
            else
            {
                $session->Start('PHPGENESISSESSID_7736298');
            }

            if(\Get::Config('Application.Session.UseAuthComponent'))
            {
                $auth = $this->GetComponent('Auth');

                // Check for login interval expiration and authorized page view
                if($session->IsSessionKeySet('login_expires'))
                {
                    if(time() > $session->Get('login_expires'))
                    {
                        $session->Destroy()->StartSecure();
                        $session->Set('AccessedRoute', $this->Router->getRouteFromPattern());
                        $this
                            ->SetError(array('Logged Out' => \Get::Config('Auth.Security.Session.ExpireMessage')))
                                ->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
                    }
                }
                else
                {
                    if(!$session->IsSessionKeySet('login_expires') AND !$this->IsPageAllowed() AND !$session->IsSessionKeySet('route_error'))
                    {
                        $session->Set('AccessedRoute', $this->Router->getRouteFromPattern());
                        $this
                            ->setError(array('Access Denied' => \Get::Config('Auth.Security.AccessDeniedMessage')))
                                ->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
                    }

                }

                // Populate User object with user defined method
                if(\Get::Config('Auth.Login.EntityRepository') AND $session->IsSessionKeySet('login_time'))
                {
                    $this->User = $auth->GetUser();

                    $tableColumn = \Get::Config('Auth.DBTable.AuthColumnName');

                    // Prevent Session Hijacking
                    if(isset($this->User->$tableColumn))
                    {
                        $browser = $this->GetSessionManager()->GetBrowserAgent();

                        $db = $this->GetDatabaseManager();

                        $password = $db->Table(\Get::Config('Auth.DBTable.AuthTableName'), array('password'))->GetOneRecordBy(array(\Get::Config('Auth.DBTable.AuthColumnName') => $this->User->$tableColumn));

                        $login_check = hash(\Get::Config('Auth.Security.PasswordEncryption'), $password->password.$browser);

                        if($login_check != $session->Get('login_string'))
                        {
                            $session->Destroy()->StartSecure();
                            $session->Set('AccessedRoute', $this->Router->getRouteFromPattern());
                            $this->SetError(\Get::Config('Auth.Security.Session.Anti-Hijacking.Message'))->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
                        }
                    }
                }
            }
        }
    }

    public function __destruct()
    {
        $this->AfterApplicationHook();
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

            $this->SetError('You need more previliges to access this page.')->ForwardTo(\Get::Config('Application.Base_Route_Name'));
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
            $this->Render(':UnderDevelopment/PageUnderDevelopment.html.php', 'This page is temporarily unavailable');
        }
    }
}