<?php

namespace Application\Core;



class Auth extends Application{

    protected
            $username,
            $password,
            $authTable,
            $authField;

    public function __construct(){

        $this->username = @$_POST[\Get::Config('Auth.Form.EmailFieldName')];

        $this->password = @$_POST[\Get::Config('Auth.Form.PasswordFieldName')];

        $this->authTable = \Get::Config('Auth.DBTable.AuthTableName');

        $this->authField = \Get::Config('Auth.DBTable.AuthColumnName');

    }

    public function forwardToLoginPage()
    {
        $this->forwardTo(\Get::Config('Auth.Login.LoginRoute'));
    }

    public function logout($message = null)
    {
        if(\Get::Config('Auth.Login.LogoutHookRoute'))
        {
            $this->forwardToController(\Get::Config('Auth.Login.LogoutHookRoute'));
        }

        $this->GetCoreObject('Session')->Destroy()->Start();

        $this->setFlash($message);

        $this->forwardTo(\Get::Config('Auth.Login.LoggedOutDefaultRoute'));
    }

    /**
     *
     * @return boolean return true on success, false on failure
     * <br /><br />Use this function to authenticate a user in your login system, will function based on the parameters provided in the Auth config file.
     */
    public function authenticateUser(){

        if(\Get::Config('Auth.Validation.Email'))
        {
            if(!$this->isValidEmail($this->username))
            {
                $this->setError(array('Invalid User' => 'Invalid characters found in email address'));
                return false;
            }
        }

        if($this->CheckBruteForceLogins())
        {
            if($this->authenticate())
            {
                $userObject = \Get::Config('Auth.Login.EntityRepository');
                $object = null;

                if(class_exists($userObject))
                {
                    $object = new $userObject();
                }
                else
                {
                    $this->forwardToController ('Class_Not_Found', array( 'controller' => $userObject, 'line' => __LINE__ ));
                }

                $objectMethod = \Get::Config('Auth.Login.UserPopulateMethod');

                $this->User = $object->$objectMethod();

                $session = $this->GetCoreObject('Session');

                $session->set('username', $this->username);

                $session->set('login_time', time());

                $session->set('login_expires', time() + \Get::Config('Auth.Security.Interval'));

                return true;
            }
            else
            {
                $this->setError(array('Invalid User' => 'Invalid username or password'));

                return false;
            }
        }
        else
        {
            $this->setError('Your account has been locked for trying too many times, try again later');
            return false;
        }
    }

    private function authenticate(){

        $db = new Database();

        $password = $hash = hash(\Get::Config('Auth.Security.PasswordEncryption'), $this->password . \Get::Config('Auth.Security.Salt'));

        $db->Table($this->authTable)->FindExistanceBy(array($this->authField => $this->username , 'password' => $password));

        if($db->GetNumberOfRows())
        {
            $userBrowser = $_SERVER['HTTP_USER_AGENT'];

            $this->GetCoreObject('Session')->Set('login_string', hash(\Get::Config('Auth.Security.PasswordEncryption'), $password.$userBrowser));
            return true;
        }
        else
            return false;
    }

    public function generatePassword($length = 10)
    {
        return $this->GenerateRandomString($length);
    }

    public function generatePasswordHash($password)
    {
        return hash(\Get::Config('Auth.PasswordEncryption'), $password.\Get::Config('Auth.Security.Salt'));
    }

    public function forwardToLoggedInPage()
    {
        if($this->GetCoreObject('Session')->IsSessionKeySet('AccessedRoute'))
        {
            $this->forwardTo($this->GetCoreObject('Session')->Get('AccessedRoute'));
        }

        $this->forwardTo(\Get::Config('Auth.Login.LoggedInDefaultRoute'));
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

    private function CheckBruteForceLogins()
    {
        $session = $this->GetCoreObject('Session');

        if($session->IsSessionKeySet('Blocked.'.$this->username))
        {
            $timeBlocked = $session->Get('Blocked.'.$this->username);

            if(($timeBlocked + \Get::Config('Auth.Security.BlockedCoolDownPeriod')) < time())
            {
                $session->Remove('Blocked.'.$this->username)->Remove('BruteForceAttempt');
            }
        }

        if($session->IsSessionKeySet('BruteForceAttempt'))
        {
            $session->Set('BruteForceAttempt', ($session->Get('BruteForceAttempt')+1));

            if($session->Get('BruteForceAttempt') >= \Get::Config('Auth.Security.MaxLoginAttempts'))
            {
                $session->Set('Blocked.'.$this->username, time());
                return false;
            }
        }
        else
            $session->Set('BruteForceAttempt', 0);

        return true;
    }
}