<?php
namespace Fmla\Controller\Factory;

use Fmla\Controller\FmlaConfigController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FmlaConfigControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new FmlaConfigController();
        $adapter = $container->get('fmla-model-adapter');
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}