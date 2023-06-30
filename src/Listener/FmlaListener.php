<?php 
namespace Fmla\Listener;

use Employee\Model\EmployeeModel;
use Fmla\Model\Fmla;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Stdlib\Parameters;
use Leave\Model\LeaveModel;
use Timecard\Controller\TimecardLineController;
use Timecard\Controller\TimecardSignatureController;
use Timecard\Model\PaycodeModel;
use Timecard\Model\TimecardLineModel;
use Timecard\Model\TimecardModel;
use Timecard\Model\Entity\TimecardEntity;
use Timecard\Traits\DateAwareTrait;

class FmlaListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    use AdapterAwareTrait;
    use DateAwareTrait;
    
    /**
     *
     * {@inheritDoc}
     * @see \Laminas\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $shared_manager = $events->getSharedManager();
        
        $this->listeners[] = $shared_manager->attach(TimecardLineController::class, 'update.pre',  [$this, 'onTimecardLineUpdatePre'], -100);
        $this->listeners[] = $shared_manager->attach(TimecardSignatureController::class, TimecardModel::EVENT_SUBMITTED,  [$this, 'onSign'], -100);
    }
    
    public function onTimecardLineUpdatePre(Event $e)
    {
        /**
         * 
         * @var TimecardLineController $controller
         */
        $controller = $e->getTarget();
        $data = [];
        
        $request = $controller->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
        }
        
        $paycode = new PaycodeModel($this->adapter);
        $paycode->read(['UUID' => $data['PAY_UUID']]);
        
        /******************************
         * RETURN IF NOT LEAVE PAY CODE
         ******************************/
        if (!$paycode->LEAVE_CODE) {
            return;
        }
        
        $timecard = new TimecardModel($this->adapter);
        $timecard->read(['UUID' => $data['TIMECARD_UUID']]);
        
        $employee = new EmployeeModel($this->adapter);
        $employee->read(['UUID' => $timecard->EMP_UUID]);
        
        $leave = new LeaveModel($this->adapter);
        $leave->read(['EMP_NUM' => $employee->EMP_NUM, 'CODE' => $paycode->LEAVE_CODE]);
        
        /******************************
         * CALCULATE HOURS
         ******************************/
        $total = 0;
        $error = false;
        $params = $controller->getRequest()->getPost();
        foreach ($this->DAYS as $DAY) {
            $total += floatval($_REQUEST[$DAY]);
            if ($total > $leave->BALANCE) {
                $diff = $total - $leave->BALANCE;
                $params->set($DAY, floatval($_REQUEST[$DAY]) - $diff);
                $total -= $diff;
                $error = true;
            }
        }
        
        if ($error) {
            $controller->flashMessenger()->addErrorMessage("You do not have enough time.");
        }
        
        $controller->getRequest()->setPost($params);
        return;
    }

    public function onSign(Event $e)
    {
        /**
         * $e->name = 'submitted'
         * @var Parameters $params
         */
        $params = $e->getParams();
        $paycode = new PaycodeModel($this->adapter);
        
        /**
         * @var TimecardEntity $entity
         */
        $entity = $params['timecard_entity'];
        
        /****************************************
         * RETURN IF NOT SIGNING AS COMPLETED
         ****************************************/
        if (! $entity->STATUS == TimecardModel::COMPLETED_STATUS) {
            return;
        }
        
        /****************************************
         * Process Timecard
         ****************************************/
        $fmla = new Fmla($this->adapter);
        $fmla->read(['EMP_UUID' => $entity->EMP_UUID, 'STATUS' => Fmla::ACTIVE_STATUS]);
        
        $total = 0;
        /**
         * @var TimecardLineModel $line
         */
        foreach ($entity->TIMECARD_LINES as $line) {
            $paycode->read(['UUID' => $line->PAY_UUID]);
            
            /****************************************
             * SKIP IF NOT FMLA PAYCODE
             ****************************************/
            if (!preg_match('/FMLA/', $paycode->CODE)) { continue; }
            
            foreach ($this->DAYS as $day) {
                $total += floatval($line->$day);
            }
        }
        
        $fmla->BANK = $fmla->BANK - $total;
        $fmla->update();
        
        return;
    }
}