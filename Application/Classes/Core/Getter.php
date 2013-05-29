<?php

namespace Application\Core;


use \Application\Interfaces\Getter as GetterInterface;

class Getter extends AppMethods implements GetterInterface{

    /**
     *
     * @param string $object
     * @param mixed $args
     * @return object $this
     * Returns an existing object or creates a new one if it does not exist in the current scope
     */
    public function GetComponent($object, $args = null) {

        $fullClassPath = '\\Application\\Components\\'.$object;

        if (!isset($this->$object) && !is_object($this->$object)) {

            if (class_exists($fullClassPath)) {

                @$this->$object = new $fullClassPath($args);
            }
            else
                trigger_error("getOjbect accepts valid class name only, $object class does not exist in ". get_called_class(), E_USER_ERROR);
        }

        return $this->$object;
    }

    public function GetCoreObject($object, $args = null){

        $fullClassPath = '\\Application\\Core\\'.$object;

        if (!isset($this->$object) && !is_object($this->$object)) {

            if (class_exists($fullClassPath)) {

                @$this->$object = new $fullClassPath($args);
            }
            else
                return $this->GetComponent ($object, $args);
        }

        return $this->$object;
    }

    public function GetObject($object, $args = null) {

        if (!isset($this->$object) && !is_object($this->$object)) {

            if (class_exists($object)) {

                @$this->$object = new $object($args);
            }
            else
                echo ' Class '.$object.' not found ' . get_called_class();
        }

        return $this->$object;
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \bundle returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetRepository($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Core\\Repositories\\';
        else
            $namespace = '\\Application\\Bundles\\'.$bundle[0].'\\Repositories\\';

        $bundle[1] .= 'Repository';

        return $this->getObject($namespace.$bundle[1]);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \bundle returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetEntity($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Core\\Entities\\';
        else
            $namespace = '\\Application\\Bundles\\'.$bundle[0].'\\Entities\\';

        $bundle[1] .= 'Entity';

        return $this->getObject($namespace.$bundle[1]);
    }

    /**
     *
     * @return Request
     */
    public function GetRequestManager ()
    {
        return $this ->GetCoreObject('Request');
    }

    /**
     *
     * @return \Application\Components\Variable
     */
    public function GetVariableManager ( )
    {
        return $this ->GetComponent('Variable');
    }

    /**
     *
     * @return Database
     */
    public function GetDatabaseManager ( )
    {
        return $this ->GetCoreObject('Database');
    }

    /**
     *
     * @return Router
     */
    public function GetRouterManager ( )
    {
        return $this ->GetCoreObject('Router');
    }

    /**
     *
     * @return Session
     */
    public function GetSessionManager ( )
    {
        return $this ->GetCoreObject('Session');
    }

    /**
     *
     * @return Auth
     */
    public function GetAuthManager ( )
    {
        return $this ->GetCoreObject('Auth');
    }

    /**
     *
     * @return \Application\Components\HTMLGenerator
     */
    public function GetHTMLGenerator ( )
    {
        return $this ->GetComponent('Variable');
    }

    /**
     *
     * @return \Application\Components\Analytics
     */
    public function GetAnalyticsManager ( )
    {
        return $this ->GetComponent('Analytics');
    }

    /**
     *
     * @return \Application\Components\ValidationEngine
     */
    public function GetValidationEngine ( )
    {
        return $this ->GetComponent('ValidationEngine');
    }

    /**
     *
     * @return \Application\Components\Mail
     */
    public function GetMailer ( )
    {
        return $this ->GetComponent('Mail');
    }

    /**
     *
     * @return \Application\Components\Zip
     */
    public function GetZipManager ( )
    {
        return $this ->GetComponent('Zip');
    }

    /**
     *
     * @return \Application\Components\Dir
     */
    public function GetDirectoryManager ( )
    {
        return $this ->GetComponent('Dir');
    }

    public function GetManager ( )
    {
        return $this;
    }

}