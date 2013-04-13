<?php

class Application extends Template{

    protected
            $htmlgenerator,
            $validationEngine,
            $auth,
            $directory,
            $zip,
            $mailer,
            $Router,
            $Request;
    public
            $User;

    public function __construct() {

        $this->Request = new Request();

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
     * @param string $object
     * @param mixed $args
     * @return object $this
     * Returns an existing object or creates a new one if it does not exist in the current scope
     */
    public function getObject($object, $args = null){

        if(!is_object($this->$object)){

            if(class_exists($object)){

                $this->$object = new $object($args);
                $this->$object->objectCreatedAt = time();
            }
            else
                trigger_error ("getOjbect accepts valid class name only, $object class does not exist", E_USER_ERROR);
        }

        return $this->$object;
    }
}