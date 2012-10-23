<?php

class Treynolds_Qconfig_Block_Adminhtml_Qsearch extends Mage_Adminhtml_Block_Template {
    public function __construct() {
        parent::__construct();
        $this->setTemplate('treynolds/qconfig/qsearch.phtml');
    }
}