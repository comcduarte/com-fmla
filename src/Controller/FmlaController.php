<?php
namespace Fmla\Controller;

use Application\Model\Entity\UserEntity;
use Components\Controller\AbstractBaseController;
use Fmla\Form\FmlaRequestForm;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
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
        $timecard_line = new TimecardLineModel($this->adapter);
        
        /**
         * 
         * @var ViewModel $view
         */
        $view = parent::updateAction();
        $view->setTemplate('fmla/update');
        $update_vars = $view->getVariables();
        $view->setVariable('update_vars', $update_vars);
        
        $data = $this->model->getTimeCardLines();
        
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