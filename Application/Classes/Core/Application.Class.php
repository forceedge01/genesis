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
            call_user_func_array(array($this->GetCoreObject('Session'), 'Start'), \Get::Config('Application.Session.Secure', 'Application.Session.HttpOnly'));

            if(@$_SESSION['login_expires'] != false)
            {
                if(time() > $_SESSION['login_expires'])
                {
                    $this->GetCoreObject('Session')->Destroy();

                    $this->setError(array('Logged Out' => 'Your session has expired, please login again.'))->forwardTo(\Get::Config('Auth.LoginRoute'));
                }

            }
            else
            {
                if(!@isset($_SESSION['login_expires']) AND $this->checkExceptionRoutes() AND !isset($_SESSION['routeError']))
                {
                    $this->setError(array('Access Denied' => 'You need to login to access this page.'))->forwardTo(\Get::Config('Auth.LoginRoute'));
                }

            }

            if(\Get::Config('Auth.Entity'))
            {
                $userObject = \Get::Config('Auth.EntityRepository');
                $objectMethod = \Get::Config('Auth.UserPopulateMethod');

                if(class_exists($userObject))
                    $this->User = new $userObject();
                else{

                    echo HOW_TO_CREATE_A_BUNDLE;
                    exit;
                }

                $this->User->$objectMethod(@$_SESSION['email']);
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

    /**
     *
     * @return \Application\Core\Request Object
     */
//    public function Request(){
//
//        return $this->Request;
//    }

    /**
     *
     * @return \Application\Core\Router Object
     */
//    public function Router(){
//
//        return $this->Router;
//    }

    /**
     *
     * @return \Application\Core\Response Object
     */
//    public function Response(){
//
//        return $this->Response;
//    }
}