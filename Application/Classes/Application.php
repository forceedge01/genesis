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

    /**
     * Checks to see if the user is logged into the application or not.
     */
    public function isLoggedIn(){

        if(!empty($_SESSION['login_expires']))
            return true;
        else
            return false;
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

            $this->setError('You need more previliges to access this page.');
            $this->forwardTo(APPLICATION_BASE_ROUTE_NAME);
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
     * @return boolean
     * Returns true if the request is an ajax request
     */
    public function isAjax(){

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            return true;
        }
        else{

            return false;
        }

    }

    /**
     *
     * @return boolean
     * Returns true if the request is a post request
     */
    public function isPost(){

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            return true;
        }
        else{

            return false;
        }
    }

    /**
     *
     * @return boolean
     * Returns true if the request is an get request
     */
    public function isGet(){

        if($_SERVER['REQUEST_METHOD'] == 'GET') {

            return true;
        }
        else{

            return false;
        }
    }

    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @param type $Value - Value of the cookie your setting up
     * @param type $time - Expiration time, has to be in seconds.
     * @return boolean
     * Returns true on successful cookie setup.
     */
    protected function setCookie($Name, $Value , $time = 2592000){

        setcookie($Name, '', -(time() + 2592000));
        if(setcookie($Name, $Value, time() + $time, '/'))
             return true;
        else
            return false;
    }


    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @return boolean
     * Returns true on successful cookie unset.
     */
    protected function unsetCookie($Name){

        if(setcookie($Name, '', -(time() + 2592000)))
            return true;
        else
            return false;

    }

}