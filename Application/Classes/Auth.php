<?php

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

            $this->User = new $userObject();

            $_SESSION['email'] = $this->username;

            $_SESSION['login_time'] = time();

//            $_SESSION['login_expires'] = time() + SESSION_TIME_INTERVAL;

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

//            $sql = "select * from {$this->authTable} where {$this->authField} = '{$this->username}' and password = '{$password}'";

            $db = new Database();

            $db->Table($this->authTable)->FindExistanceBy(array($this->authField => $this->username , 'password' => $password));

            if($db->numRows)
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
}