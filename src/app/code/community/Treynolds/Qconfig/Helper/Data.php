<?php

class Treynolds_Qconfig_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * @param $qsearch string
     * @param $sections Mage_Core_Model_Config_Element
     * @param $configRoot Varien_Simplexml_Element
     * @param $levelClause string
     * @return array
     */
    protected function getNavRecords($qsearch, $sections, $configRoot, $levelClause){
        $nav_ret = array();
        $nodes = array_merge(
            $sections->xpath('*[.//label[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'.$qsearch.'") and ../'.$levelClause.'="1"]]')
            ,$configRoot->xpath('*[./*/*[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'. $qsearch.'")]]')

        );

        /* @var $node Mage_Core_Model_Config_Element */
        foreach($nodes as $node){
            $nav_ret[] = 'section/'. $node->getName(0);
        }
        return $nav_ret;
    }
    /**
     * @param $qsearch string
     * @param $sections Mage_Core_Model_Config_Element
     * @param $configRoot Varien_Simplexml_Element
     * @param $levelClause string
     * @return array
     */
    protected function devGetNavRecords($qsearch, $sections, $configRoot, $levelClause){
        $tmp_nav_counts = array();
        $tmp_track = array();
        $nodes = $configRoot->xpath('*/*/*[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'. $qsearch.'")]');
        foreach($nodes as $node){
            $group = $node->xpath('..');
            $section = $group[0]->xpath('..');
            $section_name = $section[0]->getName();
            $tmp_track[$section_name . '/' . $group[0]->getName() . '/' . $node->getName()] = 1;
            if(isset($tmp_nav_counts[$section_name])){
                $tmp_nav_counts[$section_name][0]++;
            }
            else {
                $tmp_nav_counts[$section_name] = array(1,0);
            }
        }

        $nodes = $sections->xpath('*/groups//label[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'.$qsearch.'") and ../'.$levelClause.'="1"]');

        foreach($nodes as $node){
            $path = array();
            $parent = $node->xpath('.');
            $sanity = 0;
            while($parent!==false && count($parent)>0 && $parent[0]->getName()!='sections' && $sanity++ < 10){
                $path[] = $parent[0]->getName();
                $parent = $parent[0]->xpath('./..');
            }
            $tmp_section = false;
            $tmp_group = false;
            $tmp_field = false;

            /* The count is 4 when we matched a 'group' label */
            if(count($path)==4){

                $tmp_section = $path[3];
                $tmp_group = true;//$path[3]. '/' . $path[1];

            }
            /* The count is 6 when we match a 'field' label */
            else if(count($path)==6) {
                $tmp_section = $path[5];
                $tmp_field = $path[5] . '/' . $path[3] . '/' . $path[1];
            }


            if($tmp_section!==false){
                if($tmp_group){
                    if(isset($tmp_nav_counts[$tmp_section])){
                        $tmp_nav_counts[$tmp_section][1]++;
                    }
                    else {
                        $tmp_nav_counts[$tmp_section]= array(0,1);
                    }
                }

                if($tmp_field && !isset($tmp_track[$tmp_field])){
                    $tmp_track[$tmp_field] = 1;
                    if(isset($tmp_nav_counts[$tmp_section])){
                        $tmp_nav_counts[$tmp_section][0]++;

                    }
                    else {
                        $tmp_nav_counts[$tmp_section]= array(1,0);

                    }
                }
            }

        }
        $nav_ret = array();
        foreach($tmp_nav_counts as $section=>$counts){
            $nav_ret[] = array('section/'.$section, $counts[0], $counts[1]);
        }


        return $nav_ret;
    }

    /**
     * @param $qsearch string
     * @param $current string
     * @param $sections Mage_Core_Model_Config_Element
     * @param $levelClause string
     * @return array
     */
    protected function getGroupAndFieldRecordsByLabel($qsearch, $current, $sections, $levelClause){
        $group_ret = array();
        $field_ret = array();
        $nodes = $sections->xpath($current . '/groups//label[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'.$qsearch.'") and ../'.$levelClause.'="1"]');

        foreach($nodes as $node){
            $path = array();
            $parent = $node->xpath('.');
            $sanity = 0;
            while($parent[0]->getName()!=$current && $sanity++ < 10){
                $path[] = $parent[0]->getName();
                $parent = $parent[0]->xpath('./..');
            }
            $path[] = $current;
            /* The count is 4 when we matched a 'group' label */
            if(count($path)==4){
                $group_ret[] = $path[3]. '_' . $path[1];
            }
            /* The count is 6 when we match a 'field' label */
            else if(count($path)==6) {
                $group_ret[] = $path[5]. '_' . $path[3];
                $field_ret[] ='row_' .  $path[5] . '_' . $path[3] . '_' . $path[1];
            }

        }

        return array($group_ret, $field_ret);
    }

    /**
     * @param $qsearch string
     * @param $current string
     * @param $configRoot Varien_Simplexml_Element
     * @return array
     */
    protected function getGroupAndFieldRecordsByValue($qsearch, $current, $configRoot){
        $group_ret = array();
        $field_ret = array();

        $nodes = $configRoot->xpath($current . '//*[contains(translate(text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "'. $qsearch.'")]');
        foreach($nodes as $node){
            $path = array();

            $parent = $node->xpath('.');
            $sanity = 0;
            while($parent[0]->getName()!=$current && $sanity++ < 10){
                $path[] = $parent[0]->getName();
                $parent = $parent[0]->xpath('./..');
            }
            $path[] = $current;
            if(count($path)==3){
                $field_ret[] = 'row_' . $path[2] . '_' . $path[1] . '_' . $path[0];
                $group_ret[] = $path[2] . '_' . $path[1];
            }

        }


        return array($group_ret, $field_ret);
    }

    /**
     * This function will load a module's system.xml file and find all fields in it. Does not
     * actually do string searching, just finds everything defined.
     *
     * @param $module string
     * @param $current string
     * @param $levelClause string
     * @return array
     */
    protected function getModuleSpecificRecords($module, $current, $levelClause) {

        $module = uc_words($module);

        /* @var $conf Mage_Core_Model_Config_System */
        $conf = Mage::getModel('core/config_system');
        $conf->load($module);
        /* @var $sections Mage_Core_Model_Config_Element */

        $sections = $conf->getNode('sections');
        if(!$sections){
            return array(array(),array(),array());
        }
        $nodes = $sections->xpath('*[.//label[../'.$levelClause.'="1"]]');


        //$nodes = $sections->xpath($current . '/groups//label[../'.$levelClause.'="1"]');
        $nav_ret = array();
        $group_ret = array();
        $field_ret = array();
        /* @var $node Mage_Core_Model_Config_Element */
        foreach($nodes as $node){
            $nav_ret[] = 'section/'. $node->getName(0);
        }

        $nodes = $sections->xpath($current . '/groups//label[../'.$levelClause.'="1"]');
        foreach($nodes as $node){
            $path = array();
            $parent = $node->xpath('.');
            $sanity = 0;
            while($parent[0]->getName()!=$current && $sanity++ < 10){
                $path[] = $parent[0]->getName();
                $parent = $parent[0]->xpath('./..');
            }
            $path[] = $current;
            /* The count is 4 when we matched a 'group' label */
            if(count($path)==4){
                $group_ret[] = $path[3]. '_' . $path[1];
            }
            /* The count is 6 when we match a 'field' label */
            else if(count($path)==6) {
                $group_ret[] = $path[5]. '_' . $path[3];
                $field_ret[] ='row_' .  $path[5] . '_' . $path[3] . '_' . $path[1];
            }

        }

        return array($nav_ret, $group_ret, $field_ret);
    }

    /**
     * @param $qsearch string Query String
     * @param $current string The current section of config you are viewing
     * @param $website string The current website you are under. Can be null or empty string
     * @param $store string The store view you are under. Can be null or empty string
     * @return array with keys (nav, group, field), each of which is an array of strings
     */
    public function getQuickSearchResults($qsearch, $current, $website, $store){
        if(is_null($current)){
            $current = 'general';//This is currently not needed. Parameter gets set in adminhtml/system_config_tabs:122
        }

        $qsearch =  trim(strtolower($qsearch));

        if(strlen($qsearch)==0){
            return array('nav'=>array(),'group'=>array(), 'field'=>array());
        }

        $qsearch = preg_replace('/("|\[|\]|\(|\))/','',$qsearch);
        $levelClause = $this->getLevelClause($website, $store);

        if(!preg_match('/^module:(.+)/', $qsearch, $matches)){
            /* @var $formBlock Mage_Adminhtml_Block_System_Config_Form */
            $formBlock = Mage::app()->getLayout()->createBlock('adminhtml/system_config_form');
            /* @var $sections Varien_Simplexml_Element */
            $configRoot = $formBlock->getConfigRoot();
            /* @var $sections Mage_Core_Model_Config_Element */
            $sections = $this->getSections($current, $website, $store);
            /**
             * First, get the top-level nodes for the left-hand nav.
             */
            $nav_ret = $this->getNavRecords($qsearch, $sections, $configRoot, $levelClause);


            /**
             * For finding the elements on your page we have to do things a little different
             * We can't combine the xpath because we are grabbing the lowest level nodes
             * and since the xml structure of the Config differs from the structure of the
             * config display xml the parsing is slightly different.
             * Essentially, in the config display xml there is a max depth and there are
             * filler tags (groups, fields). In the actual config xml there aren't fillers
             * and the depth can be more variable.
             *
             * This results in an array with duplicates, but that doesn't have much effect
             * on the front-end.
             */
            /* Config display xml for the page you are on */
            $by_label = $this->getGroupAndFieldRecordsByLabel($qsearch, $current, $sections, $levelClause);
            /* Next we get the actual config xml for the page you are on */
            $by_value = $this->getGroupAndFieldRecordsByValue($qsearch, $current, $configRoot);
            $group_ret = array_merge($by_value[0], $by_label[0]);
            $field_ret = array_merge($by_value[1], $by_label[1]);
        }
        else {
            list($nav_ret, $group_ret, $field_ret) = $this->getModuleSpecificRecords($matches[1], $current, $levelClause);

        }
        /* Finally, we handle edge cases */
        //TODO: Figure out how to handle edge cases



        return array('nav'=>$nav_ret, 'group'=>$group_ret, 'field'=>$field_ret);
    }

    /**
     * Translate $sections
     *
     * @param $sections
     */
    protected function translateSections(&$sections) {
        $configFields = Mage::getSingleton('adminhtml/config');
        foreach($sections->children() as $section) {
            $helperName = $configFields->getAttributeModule($section);
            $section->label = Mage::helper($helperName)->__((string)$section->label);

            foreach($section->groups->children() as $group) {
                $helperName = $configFields->getAttributeModule($section, $group);
                $group->label = Mage::helper($helperName)->__((string)$group->label);

                if ($group->fields) {
                    foreach($group->fields->children() as $element) {
                        $helperName = $configFields->getAttributeModule($section, $group, $element);
                        $element->label =  Mage::helper($helperName)->__((string)$element->label);
                        $element->hint  = (string)$element->hint ? Mage::helper($helperName)->__((string)$element->hint) : '';
                    }
                }
            }
        }
    }

    /**
     * @param $current string
     * @param $website string
     * @param $store string
     * @return Mage_Core_Model_Config_Element
     */
    protected function getSections($current, $website, $store){
        /* TODO Have a look at Mage_Adminhtml_Block_System_Config_Form::initFields
         there is a fieldPrefix involved which does not seem to be processed here
         (but also might not be used at all)
        */
        /* @var $cache Mage_Core_Model_Cache */
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $cache = Mage::getSingleton('core/cache');
        $cache_id = 'treynolds_qcache_' . $website . '_' . $store . '_' . $locale;
        /* Check the cache */
        /* @var $sections Mage_Core_Model_Config_Element */
        $sections = null;
        /* @var $sections_xml string */
        $sections_xml = $cache->load($cache_id);
        if(!$sections_xml){
            /* @var $configFields Mage_Adminhtml_Model_Config */
            $configFields = Mage::getSingleton('adminhtml/config');
            $sections = $configFields->getSections($current);

            $this->translateSections($sections);
            $cache->save($sections->asXml(), $cache_id, array(Mage_Core_Model_Config::CACHE_TAG));
        }
        else {
            $sections = new Mage_Core_Model_Config_Element($sections_xml);
        }

        return $sections;
    }

    /**
     * @return array where the key is a string to match qsearch
     *         and the value is an array of xpath clauses
     */
    protected function getNavEdgeCases(){
        return array('yes'=>1, 'no'=>0, 'enabled'=>1, 'disabled'=>0);
    }

    /**
     * Need to check the "show_in_X" tags in system.xml files
     * @param $website string
     * @param $store string
     * @return string
     */
    protected function getLevelClause($website, $store){
        if(!is_null($store) && strlen($store)>0){
            return 'show_in_store';
        }
        if(!is_null($website) && strlen($website)>0){
            return 'show_in_website';
        }
        return 'show_in_default';
    }
}