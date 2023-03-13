<?php
namespace Fmla\Form;

use Acl\Traits\AclAwareTrait;
use Components\Form\AbstractBaseForm;
use Components\Form\Element\DatabaseSelect;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Text;

class FmlaForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    use AclAwareTrait;
    
    public $employee_adapter;
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'DATE_START',
            'type' => Date::class,
            'attributes' => [
                'id' => 'DATE_START',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'DATE_START',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'DATE_END',
            'type' => Date::class,
            'attributes' => [
                'id' => 'DATE_END',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'DATE_END',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'BANK',
            'type' => Text::class,
            'attributes' => [
                'id' => 'BANK',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Bank',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'MAX_HOURS',
            'type' => Text::class,
            'attributes' => [
                'id' => 'MAX_HOURS',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Max Hours',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'EMP_UUID',
            'type' => DatabaseSelect::class,
            'attributes' => [
                'id' => 'EMP_UUID',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Employee UUID',
                'database_table' => 'employees',
                'database_id_column' => 'UUID',
                'database_value_columns' => [
                    'EMP_NUM',
                    'LNAME',
                    'FNAME',
                ],
                'database_adapter' => $this->employee_adapter,
            ],
        ],['priority' => 100]);
    }
}