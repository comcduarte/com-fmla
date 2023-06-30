<?php
namespace Fmla\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class FmlaRecords extends AbstractHelper
{
    public $records;
    
    public function __invoke()
    {
        return $this->render();
    }
    
    public function setFmlaRecords($records)
    {
        $this->records = $records;
        return $this;
    }
    
    public function getFmlaRecords()
    {
        return $this->records;
    }
    
    public function render()
    {
        $html = '<h1>Hello World</h1>';
        return $html;
    }
}