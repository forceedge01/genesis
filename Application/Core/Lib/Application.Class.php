<?php

namespace Application\Core;



class Application extends Template implements Interfaces\Application{

    public
        $Request,
        $User;

    public function __construct() {

        parent::__construct();

        $this->BeforeApplicationHook();
        $this->Request = $this->GetCoreObject('Request');

        if(\Get::Config('Application.Session.Enabled'))
        {
            $session = $this->startSession();

            if(\Get::Config('Application.Session.UseAuthComponent'))
            {
                $this->GetComponent('Auth')->Init($session);
            }
        }
    }

    public function __destruct()
    {
        $this->AfterApplicationHook();
    }

    public function startSession()
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

        return $session;
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