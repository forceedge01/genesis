<?php

namespace Application\Core;


use \Application\Core\Interfaces\ObjectManager as ObjectManagerInterface;

abstract class ObjectManager extends Variable implements ObjectManagerInterface{

    /**
     *
     * @param string $object
     * @param mixed $args
     * @return object $this
     * Returns an existing object or creates a new one if it does not exist in the current scope
     * Loads components config files in Application/Resources/Config folder
     */
    public function GetComponent($object, $args = null) {

        if (!isset($this->$object))
        {
            Loader::LoadComponent($object);
            $classNamespace = $this->DirectoryToNamespace(\Get::Config('APPDIRS.COMPONENTS.BASE_FOLDER')).$object;
            $dependencies = \Get::Config("$object.Dependencies");

            if($args)
                $dependencies[] = $args;

            if (class_exists($classNamespace, false))
            {
                if(is_array($dependencies))
                    $this->$object = $this->GetCoreObject ('DependencyInjector')->Inject($classNamespace, $dependencies);
                else
                    $this->$object = $this->InstantiateObject ($classNamespace, $args);
            }
            else
            {
                return false;
            }
        }

        return $this->$object;
    }

    /**
     *
     * @param type $obj
     * @param type $args
     * @return \Application\Core\obj
     */
    public function InstantiateObject($obj, $args = null)
    {
        return new $obj($args);
    }

    /**
     *
     * @param type $object
     * @param type $args
     * @param type $return
     * @return boolean
     */
    public function GetCoreObject($object, $args = null){

        if (!isset($this->$object))
        {
            $fullClassPath = $this->DirectoryToNamespace(\Get::Config('APPDIRS.CORE.BASE_FOLDER')).$object;

            if (class_exists($fullClassPath, false))
            {
                $this->$object = $this->InstantiateObject($fullClassPath, $args);
            }
            else
            {
                return false;
            }
        }

        return $this->$object;
    }

    /**
     *
     * @param type $object
     * @param type $args
     * @return boolean
     */
    public function GetObject($object, $args = null) {

        list($object, $type) = $this->ExplodeAndGetLastChunk($object, '\\');

        if((!isset($this->$object)))
        {
            if($type)
            {
                if($type == 'Core')
                {
                    $this->$object = $this->GetCoreObject($object, $args);
                }
                else
                {
                    $this->$object = $this->GetComponent($object, $args);
                }
            }
            else
            {
                $this->GetCoreObject($object);

                if(!isset($this->$object))
                {
                    $this->GetComponent($object);

                    if(!isset($this->$object))
                    {
                        if(class_exists($object, false))
                        {
                            $this->$object = $this->InstantiateObject($object, $args);
                        }

                        if(!isset($this->$object))
                            return false;
                    }
                }
            }
        }

        return $this->$object;
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Repositories\ApplicationRepository returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetRepository($bundleColonEntityName){

        list($bundle, $repository) = explode(':', $bundleColonEntityName);
        $namespace = '\\Bundles\\'.$this->GetBundleNameSpace($bundle).'\\Repositories\\'.$repository.'Repository';

        if(!class_exists($namespace, false))
            Loader::LoadBundleRepositories($this->GetBundleFromName ($bundle));

        return $this->InstantiateObject($namespace);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Repositories\ApplicationRepository returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetModel($bundleColonEntityName){

        list($bundle, $model) = explode(':', $bundleColonEntityName);
        $namespace = '\\Bundles\\'.$this->GetBundleNameSpace($bundle).'\\Models\\'.$model.'Model';

        if(!class_exists($namespace, false))
            Loader::LoadBundleModel($this->GetBundleFromName ($bundle));

        return $this->InstantiateObject($namespace);
    }

    /**
     *
     * @param type $bundleColonEntityName
     * @return \Application\Entities\ApplicationEntity returns an entity object
     * @example $this->getBundleEntity('WelcomeBundle:Welcome')->GetAll();
     */
    public function GetEntity($bundleColonEntityName){

        list($bundle, $entity) = explode(':', $bundleColonEntityName);
        $namespace = '\\Bundles\\'.$this->GetBundleNameSpace($bundle).'\\Entities\\'.$entity.'Entity';

        if(!class_exists($namespace, false))
            Loader::LoadBundleEntities($this->GetBundleFromName ($bundle));

        return $this->InstantiateObject($namespace);
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
        return $this ->GetCoreObject('Variable');
    }

    /**
     *
     * @return DatabaseManager
     */
    public function GetDatabaseManager ( )
    {
        return $this ->GetCoreObject('DatabaseManager');
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
     * @return \Application\Components\HTMLGenerator
     */
    public function GetHTMLGenerator ( )
    {
        return $this ->GetComponent('HTMLGenerator');
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
        return $this ->GetComponent('Mailer');
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
        foreach(Loader::AppBundles() as $bundlePath)
        {
            $ch = explode('/', $bundlePath);
            $bundleName = end($ch);

            if($bundleName == $bundle)
                return $bundlePath;
        }
    }

    public function GetBundleNameSpace($bundle)
    {
        return str_replace('/','\\', $this->GetBundleFromName($bundle));
    }

    public function GetClassFromNameSpacedClass($namespacedClass){

        return substr($namespacedClass, (strrpos($namespacedClass, '\\'))+1);
    }

    public function GetClassFromNameSpacedController($namespacedClass){

        return str_replace('Controller', '', substr($namespacedClass, (strrpos($namespacedClass, '\\'))+1));
    }

    public function GetTableNameFromNameSpacedEntity($namespacedClass){

        return str_replace(array('Repository', 'Entity'), '', $this->GetClassFromNameSpacedClass($namespacedClass));
    }

    public function GetClassFromNameSpacedModel($namespacedClass){

        return str_replace('Model', '', $this->GetClassFromNameSpacedClass($namespacedClass));
    }

    public static function GetNamespaceFromFilePath($filePath)
    {
        $search = array(\Get::Config('APPDIRS.SOURCE_FOLDER'), '/', '.php');
        $replace = array('\\', '\\');

        return str_replace($search, $replace, $filePath);
    }

    public static function GetNamespaceFromMultipleFiles($files)
    {
        $result = array();

        foreach($files as $file)
            $result[] = self::GetNamespaceFromFilePath ($file);

        return $result;
    }

    public function DirectoryToNamespace($dir)
    {
        return str_replace(array(ROOT ,'/'), '\\', $dir);
    }

    /**
     *
     * @param type $variable
     * @param type $separator
     * @return mixed
     * Returns value at last index if $separator is found in string, if not then returns the string itself
     */
    public function ExplodeAndGetLastChunk($variable, $separator)
    {
        $chunks = explode($separator, $variable);

        if(!$chunks)
            return $variable;

        return explode(':', end($chunks));
    }
}