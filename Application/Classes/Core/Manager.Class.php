<?php

namespace Application\Core;


use \Application\Interfaces\Manager as ManagerInterface;

class Manager extends Variable implements ManagerInterface{

    /**
     *
     * @param string $object
     * @param mixed $args
     * @return object $this
     * Returns an existing object or creates a new one if it does not exist in the current scope
     */
    public function GetComponent($object, $args = null) {

        $fullClassPath = '\\Application\\Components\\'.$object;

        if (!isset($this->$object)) {

            if (class_exists($fullClassPath)) {

                @$this->$object = new $fullClassPath($args);
            }
            else
            {
                $this->GetObject($object, $args);
            }
        }

        return $this->$object;
    }

    public function GetCoreObject($object, $args = null){

        $fullClassPath = '\\Application\\Core\\'.$object;

        if (!isset($this->$object)) {

            if (class_exists($fullClassPath)) {

                @$this->$object = new $fullClassPath($args);
            }
            else
                return $this->GetObject ($object, $args);
        }

        return $this->$object;
    }

    public function GetObject($object, $variable, $args = null) {

        if (!isset($this->$variable)) {

            if (class_exists($object)) {

                @$this->$variable = new $object($args);
            }
            else
            {
                $this
                    ->SetErrorArgs(__FUNCTION__ ." accepts valid class name only, $object class does not exist.", get_called_class(), 0)
                    ->ThrowError();
            }
        }

        return $this->$variable;
    }

    /**
     *
     * @param string $type entity or repository
     * @param string $bundleColonEntity
     * @return Object
     */
    protected function Get($type, $name)
    {
        $type = 'Get'.$type;
        return $this->$type($name);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Repositories\ApplicationRepository returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetRepository($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Repositories\\';
        else
            $namespace = '\\'.$this->GetBundleNameSpace($bundle[0]).'\\Repositories\\';

        $bundle[1] .= 'Repository';

        return $this->GetObject($namespace.$bundle[1], $bundle[1]);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Repositories\ApplicationRepository returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetModel($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Models\\';
        else
            $namespace = '\\'.$this->GetBundleNameSpace($bundle[0]).'\\Models\\';

        $bundle[1] .= 'Model';

        return $this->GetObject($namespace.$bundle[1], $bundle[1]);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Entities\ApplicationEntity returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetEntity($bundleColonEntityName){

        $bundle = explode(':', $bundleColonEntityName);

        if($bundle[0] == null)
            $namespace = '\\Application\\Entities\\';
        else
            $namespace = '\\'.$this->GetBundleNameSpace($bundle[0]).'\\Entities\\';

        $bundle[1] .= 'Entity';

        return $this->GetObject($namespace.$bundle[1], $bundle[1]);
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
     * @return \Application\Core\Response;
     */
    public function GetResponseManager ()
    {
        return $this ->GetCoreObject('Response');
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

    public static function GetConfigs()
    {
        return Loader::$appConfiguration;
    }

    protected function GetBundleFromName($bundle)
    {
        foreach(\Application\Core\Loader::AppBundles() as $bundlePath)
        {
            $ch = explode('/', $bundlePath);
            $bundleName = end($ch);

            if($bundleName == $bundle)
                return $bundlePath;
        }
    }

    protected function GetBundleNameSpace($bundle)
    {
        return str_replace('/','\\', $this->GetBundleFromName($bundle));
    }
}