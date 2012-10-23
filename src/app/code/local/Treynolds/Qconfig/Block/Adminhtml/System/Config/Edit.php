<?php
/* Rewrite needed as the target block is anonymous, so can't be reliably referenced in layout */
class Treynolds_Qconfig_Block_Adminhtml_System_Config_Edit extends Mage_Adminhtml_Block_System_Config_Edit {

    protected function _prepareLayout()
    {
        /* Inject our quick search block */
        $this->setChild('qsearch', $this->getLayout()->createBlock('qconfig/adminhtml_qsearch'));
        return parent::_prepareLayout();
    }
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('qsearch') . parent::getSaveButtonHtml();
    }
}