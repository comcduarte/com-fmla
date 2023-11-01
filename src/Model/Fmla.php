<?php
namespace Fmla\Model;

use Components\Model\AbstractBaseModel;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Timecard\Model\PaycodeModel;
use Timecard\Model\TimecardLineModel;
use Timecard\Traits\DateAwareTrait;

class Fmla extends AbstractBaseModel
{
    use DateAwareTrait;
    
    public $DATE_START;
    public $DATE_END;
    public $BANK;
    public $MAX_HOURS;
    public $EMP_UUID;
    
    public function __construct(Adapter $adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('fmla');
        
        array_push($this->private_attributes, 'date','today','DAYS');
        $this->setPublicAttributes();
    }
    
    public function updateBank()
    {
        $bank = $this->MAX_HOURS;
        $paycode = new PaycodeModel($this->adapter);
        
        $timecard_lines = $this->getTimeCardLines();
        foreach ($timecard_lines as $line) {
            $paycode->read(['CODE' => $line['Code']]);
            
            /****************************************
             * SKIP IF NOT FMLA PAYCODE
             ****************************************/
            if (!preg_match('/FMLA/', $paycode->CODE)) { continue; }
            
            foreach ($this->DAYS as $day) {
                $bank -= floatval($line[$day]);
            }
        }
        $this->BANK = $bank;
        return $this->update();
    }
    
    public function getTimeCardLines()
    {
        $timecard_line = new TimecardLineModel($this->adapter);
        $select = new Select();
        $select->columns([
            'UUID', 'SUN','MON','TUE','WED','THU','FRI','SAT','DAYS',
        ]);
        $select->join('time_pay_codes', 'time_pay_codes.UUID = time_cards_lines.PAY_UUID', ['Code' => 'CODE'], Join::JOIN_INNER);
        $select->join('time_cards', 'time_cards.UUID = time_cards_lines.TIMECARD_UUID',['WORK_WEEK']);
        
        $where = new Where();
        $where->equalTo('EMP_UUID', $this->EMP_UUID);
        $where->equalTo('time_cards_lines.STATUS', $timecard_line::COMPLETED_STATUS);
        $where->like('time_pay_codes.CODE', '%FMLA%');
        $where->between('time_cards.WORK_WEEK', $this->DATE_START, $this->DATE_END);
        
        $select->order('time_cards.WORK_WEEK');
        
        $timecard_line->setSelect($select);
        return $timecard_line->fetchAll($where);
    }
}