<?php
namespace Fmla\Form\Factory;

use Fmla\Form\FmlaForm;
use Fmla\Model\Fmla;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FmlaFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $form = new FmlaForm();
        $adapter = $container->get('fmla-model-adapter');
        $employee_adapter = $container->get('employee-model-adapter');
        
        $model = new Fmla($adapter);
        
        $form->setInputFilter($model->getInputFilter());
        $form->setDbAdapter($adapter);
        $form->employee_adapter = $employee_adapter;
        return $form;
    }
}