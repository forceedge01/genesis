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

        if(SESSION_ENABLED){

            if(@$_SESSION['login_expires'] != false){

                if(time() > $_SESSION['login_expires']){

                    $this->GetCoreObject('Session')->Destroy();

                    $this->setError(array('Logged Out' => 'Your session has expired, please login again.'))->forwardTo(AUTH_LOGIN_ROUTE);
                }

            }
            else{

                if(((@$_SESSION['login_expires'] == false)) &&
                    ($this->checkExceptionRoutes()) &&
                        (!isset($_SESSION['routeError'])) ){

                    $this->setError(array('Access Denied' => 'You need to login to access this page.'))->forwardTo(AUTH_LOGIN_ROUTE);
                }

            }

            if(AUTH_USER_ENTITY)
            {
                $userObject = AUTH_USER_ENTITY;
                $objectMethod = AUTH_USER_POPULATE_METHOD;

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
    public function Request(){

        return $this->Request;
    }

    /**
     * 
     * @return \Application\Core\Router Object
     */
    public function Route(){

        return $this->Router;
    }
    
    /**
     * 
     * @return \Application\Core\Response Object
     */
    public function Response(){

        return $this->Response;
    }
}