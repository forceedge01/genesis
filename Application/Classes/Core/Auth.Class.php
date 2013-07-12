<?php

namespace Application\Core;



class Auth extends Template{

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

    public function ForwardToLoginPage($message = null)
    {
        $this->SetFlash($message)->forwardTo(\Get::Config('Auth.Login.LoginRoute'));
    }

    public function Logout($message = null)
    {
        if(\Get::Config('Auth.Login.BeforeLogoutHookRoute'))
        {
            $this->ForwardToController(\Get::Config('Auth.Login.LogoutHookRoute'));
        }

        $this->GetCoreObject('Session')->Destroy()->Start();

        $this->SetFlash($message);

        if(\Get::Config('Auth.Login.AfterLogoutHookRoute'))
        {
            $this->ForwardToController(\Get::Config('Auth.Login.LogoutHookRoute'));
        }

        $this->ForwardTo(\Get::Config('Auth.Login.LoggedOutDefaultRoute'));
    }

    /**
     *
     * @param string $message Invalid username or password flash message
     * @return boolean return true on success, false on failure
     * <br /><br />Use this function to authenticate a user in your login system, will function based on the parameters provided in the Auth config file.
     */
    public function AuthenticateUser($message){

        if(\Get::Config('Auth.Validation.Email.Enable'))
        {
            if(!$this->IsValidEmail($this->username))
            {
                $this->SetError(array('Invalid User' => \Get::Config('Auth.Validation.Email.Message')));
                return false;
            }
        }

        if($this->CheckBruteForceLogins())
        {
            if($this->Authenticate())
            {
                $userObject = \Get::Config('Auth.Login.EntityRepository');
                $object = null;

                if(class_exists($userObject))
                {
                    $object = new $userObject();
                }
                else
                {
                    $this->ForwardToController ('Class_Not_Found', array( 'controller' => $userObject, 'line' => __LINE__ ));
                }

                $objectMethod = \Get::Config('Auth.Login.UserPopulateMethod');

                if($objectMethod)
                    $this->User = $object->$objectMethod();
                else
                    $this->User = $this->GetEntity('users:users')->FindBy(array($this->authField => $this->username));

                $session = $this->GetCoreObject('Session');
                $session->Set('username', $this->username);
                $session->Set('login_time', time());
                $session->Set('login_expires', time() + \Get::Config('Auth.Security.SessionInterval'));

                return true;
            }
            else
            {
                $this->setError(array('Invalid User' => $message));

                return false;
            }
        }
        else
        {
            $this->SetError(\Get::Config('Auth.Security.BruteForce.Message'));
            return false;
        }
    }

    private function Authenticate(){

        $db = $this->GetDatabaseManager();

        $password = hash(\Get::Config('Auth.Security.PasswordEncryption'), $this->password . \Get::Config('Auth.Security.Salt'));

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

    public function GeneratePassword($length = 10)
    {
        return $this->GenerateRandomString($length);
    }

    public function GeneratePasswordHash($password)
    {
        return hash(\Get::Config('Auth.Security.PasswordEncryption'), $password.\Get::Config('Auth.Security.Salt'));
    }

    public function ForwardToLoggedInPage()
    {
        if($this->GetCoreObject('Session')->IsSessionKeySet('AccessedRoute'))
        {
            $this->ForwardTo($this->GetCoreObject('Session')->Get('AccessedRoute'));
        }

        $this->ForwardTo(\Get::Config('Auth.Login.LoggedInDefaultRoute'));
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

            if(($timeBlocked + \Get::Config('Auth.Security.BruteForce.BlockedCoolDownPeriod')) < time())
            {
                $session->Remove('Blocked.'.$this->username)->Remove('BruteForceAttempt');
            }
        }

        if($session->IsSessionKeySet('BruteForceAttempt'))
        {
            $session->Set('BruteForceAttempt', ($session->Get('BruteForceAttempt')+1));

            if($session->Get('BruteForceAttempt') >= \Get::Config('Auth.Security.BruteForce.MaxLoginAttempts'))
            {
                $session->Set('Blocked.'.$this->username, time());
                return false;
            }
        }
        else
            $session->Set('BruteForceAttempt', 1);

        return true;
    }

    public function GetUser()
    {
        if($this->GetSessionManager()->Get('username'))
        {
            $userObject = \Get::Config('Auth.Login.EntityRepository');
            $object = null;

            if(class_exists($userObject))
            {
                $object = new $userObject();
            }
            else
            {
                $this->ForwardToController ('Class_Not_Found', array( 'controller' => $userObject, 'line' => __LINE__ ));
            }

            $objectMethod = \Get::Config('Auth.Login.UserPopulateMethod');

            if($objectMethod)
            {
                return $object->$objectMethod();
            }
            else
            {
                return $this->GetEntity('users:users')->FindBy(array($this->authField => $this->GetSessionManager()->Get('username')));
            }
        }
    }
}