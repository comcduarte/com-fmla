<?php
namespace Fmla\Listener\Factory;

use Fmla\Listener\FmlaListener;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FmlaListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $listener = new FmlaListener();
        $adapter = $container->get('timecard-model-adapter');
        $listener->setDbAdapter($adapter);
        return $listener;
    }
}