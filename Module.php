<?php
namespace DbSession;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use DbSession\Service\SessionDB;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    
        // session start from here
        $this->initDbSession( $e );
    }
    
    /**
     * Store session into database
     *
     * @param type $e
     */
    private function initDbSession( MvcEvent $e )
    {
        // grab the config array
        $serviceManager     = $e->getApplication()->getServiceManager();
        $config             = $serviceManager->get('config');
    
        $dbAdapter          = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $saveHandler   = new SessionDB($config['db']);
    
        $sessionConfig = new \Zend\Session\Config\SessionConfig();
        $sessionConfig->setOptions($config['session']);
    
        // pass the saveHandler to the sessionManager and start the session
        $sessionManager = new \Zend\Session\SessionManager( $sessionConfig , NULL, $saveHandler );
        $sessionManager->start();
    
        \Zend\Session\Container::setDefaultManager($sessionManager);
    
    }
}
