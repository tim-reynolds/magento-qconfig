<?php

class Treynolds_Qconfig_Adminhtml_QconfigController extends Mage_Adminhtml_Controller_Action {
    public function searchAction(){
        $_qsearch = $this->getRequest()->getParam('qsearch');
        $_website = $this->getRequest()->getParam('website');
        $_store = $this->getRequest()->getParam('store');
        $_section = $this->getRequest()->getParam('section');
        header('Content-Type: application/json');
        echo Mage::helper('core')->jsonEncode(Mage::helper('qconfig')->getQuickSearchResults($_qsearch, $_section, $_website, $_store));
        exit();
    }



}