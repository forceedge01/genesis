<?php

class Application extends Template{

    public
            $Router,
            $User,
            $Error,
            $verbose;

    public function __construct() {

        $this->Router = new Router();

        if(SESSION_ENABLED){

            if(@$_SESSION['login_expires'] != false){

                if(time() > $_SESSION['login_expires']){

                    session_destroy();

                    $this->setError(array('Logged Out' => 'Your session has expired, please login again.'));

                    $this->forwardTo(AUTH_LOGIN_ROUTE);
                }

            }
            else{

                if(((@$_SESSION['login_expires'] == false)) &&
                        ($this->checkExceptionRoutes()) &&
                                    (!isset($_SESSION['routeError'])) ){

                    $this->setError(array('Access Denied' => 'You need to login to access this page.'));

                    $this->forwardTo (AUTH_LOGIN_ROUTE);
                }

            }

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

    /**
     *
     * @param string $password: the password string you wish to hash.
     * @return string returns the hashed string.
     * <br /><br />It will generate a password hash based on the algorithm defined in the Auth config file.
     */
    public function hashPassword($password){

        return hash(AUTH_PASSWORD_ENCRYPTION_ALGORITHM, $password);
    }

    /**
     *
     * @param int $length: length of the string to generate.
     * @return a random string generated equals the length specified
     */
    public function generateRandomString($length = 10) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function isLoggedIn(){

        if(!empty($_SESSION['login_expires']))
            return true;
        else
            return false;
    }

    public function checkIfAccessableBy($roleId = 1){

        if($this->User->RoleId <= $roleId){

            return true;
        }
        else {

            $this->setError('You need more previliges to access this page.');
            $this->forwardTo(APPLICATION_BASE_ROUTE_NAME);
        }

    }

    public function userRoleIs($roleId = 1){

        if($this->User->RoleId <= $roleId){

            return true;
        }
        else {

            return false;
        }

    }

}