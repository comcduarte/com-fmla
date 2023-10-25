<?php
namespace Fmla\Controller;

use Application\Model\Entity\UserEntity;
use Components\Controller\AbstractBaseController;
use Fmla\Form\FmlaRequestForm;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\View\Model\ViewModel;
use Timecard\Model\TimecardLineModel;

class FmlaController extends AbstractBaseController
{
    public function indexAction()
    {
        $select = new Select();
        $select->columns([
            'UUID',
            'Start Date' => 'DATE_START',
            'End Date' => 'DATE_END',
            'Bank' => 'BANK',
        ]);
        $select->join('employees', 'fmla.EMP_UUID = employees.UUID', ['Emp Num' => 'EMP_NUM', 'Last Name' => 'LNAME','First Name' => 'FNAME'], Join::JOIN_INNER);
        $this->model->setSelect($select);
        
        $view = parent::indexAction();
        $view->setTemplate('base/subtable');
        
        $params = [
            [
                'route' => 'fmla/default',
                'action' => 'update',
                'key' => 'UUID',
                'label' => 'Update',
            ],
            [
                'route' => 'fmla/default',
                'action' => 'delete',
                'key' => 'UUID',
                'label' => 'Delete',
            ],
        ];
        
        $view->setvariables ([
            'params' => $params,
            'search' => true,
            'title' => 'Active FMLA',
        ]);
        
        return $view;
    }
    
    public function requestAction()
    {
        $view = new ViewModel();
        $view->setTemplate('base/subform');
        
        $form = new FmlaRequestForm();
        $form->init();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            
            $form->setData($post);
            
            if ($form->isValid()) {
                
            } else {
                
            }
            
        }
        
        $employee = new UserEntity($this->adapter);
        $employee->getUser($this->currentUser()->UUID);
        
        $form->get('UUID')->setValue($employee->employee->UUID);
        
        $view->setVariables([
            'title' => 'Request FMLA',
            'form' => $form,
        ]);
        return $view;
    }

    public function updateAction()
    {
        /**
         * 
         * @var ViewModel $view
         */
        $view = parent::updateAction();
        $view->setTemplate('fmla/update');
        $update_vars = $view->getVariables();
        $view->setVariable('update_vars', $update_vars);
        
        $timecard_line = new TimecardLineModel($this->adapter);
        $select = new Select();
        $select->columns([
            'UUID', 'SUN','MON','TUE','WED','THU','FRI','SAT','DAYS',
        ]);
        $select->join('time_pay_codes', 'time_pay_codes.UUID = time_cards_lines.PAY_UUID', ['Code' => 'CODE'], Join::JOIN_INNER);
        $select->join('time_cards', 'time_cards.UUID = time_cards_lines.TIMECARD_UUID',['WORK_WEEK']);
        
        $where = new Where();
        $where->equalTo('EMP_UUID', $this->model->EMP_UUID);
        $where->equalTo('time_cards_lines.STATUS', $timecard_line::COMPLETED_STATUS);
        $where->like('time_pay_codes.CODE', '%FMLA%');
        $where->between('time_cards.WORK_WEEK', $this->model->DATE_START, $this->model->DATE_END);
        
        $select->order('time_cards.WORK_WEEK');
        
        $timecard_line->setSelect($select);
        $data = $timecard_line->fetchAll($where);
        
        $header = [];
        if (!empty($data)) {
            $header = array_keys($data[0]);
        }
        
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $params = [];
        
        $history_vars = [
            'data' => $data,
            'primary_key' => $timecard_line->getPrimaryKey(),
            'header' => $header,
            'params' => $params,
            'route' => $route,
            'title' => 'Timecard History',
        ];
        $view->setVariable('history_vars', $history_vars);
        
        return $view;
    }
}