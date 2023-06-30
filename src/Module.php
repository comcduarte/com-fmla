<?php
namespace Fmla;

use Fmla\Listener\FmlaListener;
use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootStrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        
        $listener = $serviceManager->get(FmlaListener::class);
        $listener->attach($eventManager);
    }
}