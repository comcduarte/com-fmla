<?php
namespace Fmla\Controller\Factory;

use Fmla\Controller\FmlaController;
use Fmla\Form\FmlaForm;
use Fmla\Model\Fmla;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FmlaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new FmlaController();
        
        $adapter = $container->get('fmla-model-adapter');
        $controller->setDbAdapter($adapter);
        
        $model = new Fmla($adapter);
        
        $controller->setModel($model);
        
        $form = $container->get('FormElementManager')->get(FmlaForm::class);
        $controller->setForm($form);
        return $controller;
    }
}