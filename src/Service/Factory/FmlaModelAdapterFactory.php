<?php
namespace Fmla\Service\Factory;

use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FmlaModelAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = new Adapter($container->get('fmla-model-adapter-config'));
        return $adapter;
    }
}