<?php

namespace Auth;



use Application\Core\Template;

class Auth extends Template{

    protected
            $username,
            $password,
            $authTable,
            $authField,
            $router;

    public function __construct(){

        $this->authTable = \Get::Config('Auth.DBTable.AuthTableName');
        $this->authField = \Get::Config('Auth.DBTable.AuthColumnName');
        $this->router = $this->getComponent('Router');
    }

    public function SetPostParams()
    {
        $username = \Get::Config('Auth.Form.EmailFieldName');
        $password = \Get::Config('Auth.Form.PasswordFieldName');

        if(! isset($_POST[$username]))
            $this->ThrowError ('Post parameter field name: '.$username.' was not found');

        if(! isset($_POST[$password]))
            $this->ThrowError ('Post parameter field name: '.$password.' was not found');

        $this->username = $_POST[$username];
        $this->password = $_POST[$password];
    }

    // Refactor this whole method, consider moving to classes structure or something
    public function Init(\Application\Core\Session $session)
    {
        // Check for login interval expiration and authorized page view
        if($session->IsSessionKeySet('login_expires'))
        {
            if(time() > $session->Get('login_expires'))
            {
                $session->Destroy()->StartSecure();
                $session->Set('AccessedRoute', $this->getRouteFromPattern());
                $this
                    ->SetError(array('Logged Out' => \Get::Config('Auth.Security.Session.ExpireMessage')))
                        ->router->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
            }
        }
        else
        {
            if(!$session->IsSessionKeySet('login_expires')
                    AND !$this->GetComponent('Router')->IsPageAllowed(\Get::Config('Auth.Security.Bypass'))
                        AND !$session->IsSessionKeySet('route_error'))
            {
                $session->Set('AccessedRoute', $this->getRouteFromPattern());
                $this
                    ->setError(array('Access Denied' => \Get::Config('Auth.Security.AccessDeniedMessage')))
                        ->router->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
            }
        }

        // Populate User object with user defined method
        if(\Get::Config('Auth.Login.EntityRepository') AND $session->IsSessionKeySet('login_time'))
        {
            $this->User = $this->GetUser();

            $tableColumn = \Get::Config('Auth.DBTable.AuthColumnName');

            // Prevent Session Hijacking
            if(isset($this->User->$tableColumn))
            {
                $browser = $session->GetBrowserAgent();
                $db = $this->GetDatabaseManager();
                $password = $db->Table(\Get::Config('Auth.DBTable.AuthTableName'), array('password'))
                                ->GetOneRecordBy(array(
                                    \Get::Config('Auth.DBTable.AuthColumnName') => $this->User->$tableColumn)
                                );
                $login_check = hash(\Get::Config('Auth.Security.PasswordEncryption'), $password->password.$browser);

                if($login_check != $session->Get('login_string'))
                {
                    $session->Destroy()->StartSecure();
                    $session->Set('AccessedRoute', $this->getRouteFromPattern());
                    $this->SetError(\Get::Config('Auth.Security.Session.Anti-Hijacking.Message'))
                            ->router->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
                }
            }
        }
    }

    public function ForwardToLoginPage($message = null)
    {
        $this->SetFlash($message)->router->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
    }

    public function Logout($message = null)
    {
        $this->GetCoreObject('Session')->Destroy()->StartSecure(\Get::Config('Application.Session.Secure.HttpsSecure'), \Get::Config('Application.Session.Secure.HttpOnly'))->RegenerateId(true);
        $this->SetFlash($message)->router->ForwardTo(\Get::Config('Auth.Login.LoginRoute'));
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
            $password = $this->Authenticate();

            if($password)
            {
                $session = $this->GetCoreObject('Session');
                $session->RegenerateId();
                $session->Clear();
                $session->Set('username', $this->username);
                $session->Set('login_time', time());
                $session->Set('login_expires', time() + \Get::Config('Auth.Security.Session.Interval'));
                $userBrowser = $this->GetSessionManager()->GetBrowserAgent();
                $login_string = hash(\Get::Config('Auth.Security.PasswordEncryption'), $password.$userBrowser);
                $session->Set('login_string', $login_string);

                return $this->GetUser();
            }
            else
            {
                $this->setError(array('Invalid User' => $message));
                return false;
            }
        }
        else
        {
            $this->SetError(\Get::Config('Auth.Security.Session.BruteForce.Message'));
            return false;
        }
    }

    private function Authenticate(){

        $db = $this->GetDatabaseManager();

        $password = hash(\Get::Config('Auth.Security.PasswordEncryption'), $this->password . \Get::Config('Auth.Security.Salt'));

        $db->Table($this->authTable)->FindExistanceBy(array($this->authField => $this->username , 'password' => $password));

        if($db->GetNumberOfRows())
        {
            return $password;
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
            $this->router->ForwardTo($this->GetCoreObject('Session')->Get('AccessedRoute'));
        }

        $this->router->ForwardTo(\Get::Config('Auth.Login.LoggedInDefaultRoute'));
    }

    /**
     *
     * @param string $email - the email you want to validate
     * @return boolean returns true on success false on failure.
     */
    public function IsValidEmail($email){

        $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';

        if(preg_match($pattern, $email))
            return true;
        else
            return false;
    }

    /**
     * Checks to see if the user is logged into the application or not.
     */
    public function IsLoggedIn(){

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

            if(($timeBlocked + \Get::Config('Auth.Security.Session.BruteForce.BlockedCoolDownPeriod')) < time())
            {
                $session->Remove('Blocked.'.$this->username)->Remove('BruteForceAttempt');
            }
        }

        if($session->IsSessionKeySet('BruteForceAttempt'))
        {
            $session->Set('BruteForceAttempt', ($session->Get('BruteForceAttempt')+1));

            if($session->Get('BruteForceAttempt') >= \Get::Config('Auth.Security.Session.BruteForce.MaxLoginAttempts'))
            {
                $session->Set('Blocked.'.$this->username, time());

                if(\Get::Config('Auth.Security.Session.BruteForce.MailUserOnBlock.Enabled') AND $this->IsValidEmail($this->username))
                    $this
                        ->GetComponent('Mailer')
                            ->send($this->username, \Get::Config('Auth.Security.Session.BruteForce.MailUserOnBlock.Subject'), \Get::Config('Auth.Security.Session.BruteForce.MailUserOnBlock.Body'));

                return false;
            }
        }
        else
            $session->Set('BruteForceAttempt', 1);

        return true;
    }

    public function GetUser()
    {
        $username = $this->GetSessionManager()->Get('username');

        if($username)
        {

            $userEntity = \Get::Config('Auth.Login.EntityRepository');
            $objectMethod = \Get::Config('Auth.Login.UserPopulateMethod');

            $repo = $this->GetRepository($userEntity);

            if(method_exists($repo, $objectMethod))
            {
                return $repo->$objectMethod();
            }
            else
            {
                return $repo->findOneBy(array($this->authField => $username));
            }
        }
    }
}