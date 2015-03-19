<?php

class Treynolds_Qconfig_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    protected $_overrides = array();
    protected $_website_map = null;
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        /* @var $form Mage_Adminhtml_Block_System_Config_Form */
        $form = $this->getData('form');
        $section = $form->getSectionCode();

        $overrides = $this->_getOverrides();

        /* @var $field Varien_Data_Form_Element_Abstract */
        /** @noinspection PhpUndefinedMethodInspection */
        foreach ($element->getSortedElements() as $field) {
            $temp_html = $field->toHtml();
            $group_matches = array();
            preg_match('/groups\\[([^\\]]+)\\]\\[[^\\]]+\\]\\[([^\\]]+)\\]/', $field->getName(), $group_matches);
            $path = null;
            if(count($group_matches) == 3)
            {
                $group = $group_matches[1];
                $name = $group_matches[2];
                $path = $section . '/' . $group . '/' . $name;
            }
            if(isset($overrides[$path]))
            {
                $temp_html = preg_replace('/(class="scope-label">\[[^\]]+\])/','$1 ' . $overrides[$path], $temp_html);
            }
            $html .= $temp_html;
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getOverrides()
    {
        /* @var $form Mage_Adminhtml_Block_System_Config_Form */
        $form = $this->getData('form');
        $section = $form->getSectionCode();
        if(!isset($this->_overrides[$section]))
        {
            $site_mapping = $this->_getWebsiteMap();
            $override_template = array();
            for($i = 0; $i < count($site_mapping); $i ++)
            {
                $override_template[$i] = '_';
            }
            /** @var $config_data Mage_Core_Model_Resource_Config_Data_Collection */
            $config_data = Mage::getModel('core/config_data')->getCollection();
            $config_data->addFieldToFilter('path', array('like' => $section . '/%'));
            $config_data->addFieldToFilter('scope', array('neq' => 'default'));
            $config_data->addFieldToSelect(array('config_id', 'scope', 'scope_id', 'path'));
            $config_data->load();

            $override = array();
            /* @var $config_value Mage_Core_Model_Config_Data */
            foreach($config_data as $config_value)
            {
                $path = $config_value->getPath();
                if(!isset($override[$path]))
                {
                    $override[$path] = array_merge($override_template, array());
                }
                $site_map = $site_mapping[$config_value->getScope() . '_' . $config_value->getScopeId()];
                $highlight = '';
                if(isset($site_map['active']) && $site_map['active'])
                {
                    $highlight = 'style="background-color:green;color:white;font-weight:bold;"';
                }

                $override[$path][$site_map['offset']] = sprintf('<span title="%s %s" %s>X</span>', Mage::helper('qconfig')->__('Overridden In'), $site_map['label'], $highlight);
            }
            foreach($override as $path => $final_override)
            {
                $override[$path] = '[' . implode('', $final_override) . ']';
            }

            $this->_overrides[$section] = $override;
        }

        return $this->_overrides[$section];
    }

    protected function _getWebsiteMap()
    {
        if(!$this->_website_map)
        {
            $current_website = Mage::app()->getRequest()->getParam('website');
            $current_store = Mage::app()->getRequest()->getParam('store');
            $offset = 0;
            $map = array();
            /* @var $website Mage_Core_Model_Website */
            foreach(Mage::app()->getWebsites() as $website)
            {
                $map['websites_' . $website->getId()] = array(
                    'label' => $website->getName(),
                    'offset' => $offset++,
                    'noted_parent' => $website->getCode() == $current_website,
                    'active' => $website->getCode() == $current_website && !$current_store
                );
                /* @var $store Mage_Core_Model_Store */
                foreach($website->getStores() as $store)
                {
                    $map['stores_' . $store->getId()] = array(
                        'label' => $store->getName(),
                        'offset' => $offset++,
                        'active' => $current_store == $store->getCode(),
                        'noted_child' => $current_website == $store->getWebsite()->getCode()
                    );
                }
            }
            $this->_website_map = $map;
        }
        return $this->_website_map;
    }
}