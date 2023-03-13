<?php
namespace Fmla\Controller;

use Application\Model\Entity\UserEntity;
use Components\Controller\AbstractBaseController;
use Fmla\Form\FmlaRequestForm;
use Laminas\View\Model\ViewModel;

class FmlaController extends AbstractBaseController
{
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
}