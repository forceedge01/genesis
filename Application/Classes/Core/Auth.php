<?php

namespace Application\Core;



class Auth extends Application{

    protected
            $username,
            $password,
            $authTable,
            $authField;

    public
            $User;

    public function __construct(){

        $this->username = $_POST[AUTH_EMAIL_FIELD_NAME];

        $this->password = $_POST[AUTH_PASSWORD_FIELD_NAME];

        $this->authTable = AUTH_TABLE_NAME;

        $this->authField = AUTH_FIELD_IN_TABLE_NAME;

    }

    /**
     *
     * @return boolean return true on success, false on failure
     * <br /><br />Use this function to authenticate a user in your login system, will function based on the parameters provided in the Auth config file.
     */
    public function authenticateUser(){

        if(AUTH_VALIDATE_USERNAME_IF_EMAIL)
            if(!$this->isValidEmail($this->username)){

                $this->setError(array('Invalid User' => 'Invalid characters found in email address'));

                return false;
            }

        if($this->authenticate())
        {
            $userObject = AUTH_USER_ENTITY;

            if(class_exists($userObject))
                $this->User = new $userObject();
            else
                $this->forwardToController ('Class_Not_Found', array( 'controller' => $userObject, 'line' => __LINE__ ));
            
            $this->GetCoreObject('Session')->start();

            $this->GetCoreObject('Session')->set('email', $this->username);

            $this->GetCoreObject('Session')->set('login_time', time());

            $objectMethod = AUTH_USER_POPULATE_METHOD;

            $this->User->$objectMethod();

            return true;
        }
        else
        {

            $this->setError(array('Invalid User' => 'Invalid username or password'));

            return false;

        }
    }

    private function authenticate(){

            $password = $hash = hash(AUTH_PASSWORD_ENCRYPTION_ALGORITHM, $this->password);

            $db = new Database();

            $db->Table($this->authTable)->FindExistanceBy(array($this->authField => $this->username , 'password' => $password));

            if($db->GetNumberOfRows())
                return true;
            else
                return false;
    }

    /**
     *
     * @param string $email - the email you want to validate
     * @return boolean returns true on success false on failure.
     */
    public function isValidEmail($email){

        $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';

        if(preg_match($pattern, $email))
            return true;
        else
            return false;
    }

    public function GetCurrentUser(){

        return $this->User;
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
}