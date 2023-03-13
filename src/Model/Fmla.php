<?php
namespace Fmla\Model;

use Components\Model\AbstractBaseModel;
use Laminas\Db\Adapter\Adapter;

class Fmla extends AbstractBaseModel
{
    public $DATE_START;
    public $DATE_END;
    public $BANK;
    public $MAX_HOURS;
    public $EMP_UUID;
    
    public function __construct(Adapter $adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('fmla');
        
    }
}