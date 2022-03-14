<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Custom extends AbstractNetwork
{

    /**
     * Field data
     * @var Array
     */
    protected $_fieldsData  = [];
    
    /**
     * Get sectipn
     * @return string 
     */
    protected function _getSections()
    {
        return $this->getAffiliate()->getPageSections();
    }

    /**
     * Set fields data
     * @return $this 
     */
    protected function _setFieldsData()
    {
        $affiliate      = $this->getAffiliate();

        $includeonValues = ['-- None --'];
        $collection = $this->getIncludeonCollection();
        foreach ($collection as $item) {
            $includeonValues[$item->getId()] = $item->getName();
        }
        
        if (!empty($this->_getSections()) && count($this->_getSections())) {

            foreach ($this->_getSections() as $section) {

                $sKey = $section['key'];
                $getSectionLibrary      = 'getSection'.ucfirst($sKey).'Library';
                $getSectionCode         = 'getSection'.ucfirst($sKey).'Code';
                $getSectionIncludeonId  = 'getSection'.ucfirst($sKey).'IncludeonId';
                //$getSectionIncludeonId    = 'getSection'.ucfirst($sKey).'IncludeonId';
                
                $this->_fieldsData[$sKey.'_library'] = [
                    'name'      => 'section_'.$sKey.'_library',
                    'label'     => __('JavaScript Library File'),
                    'required'  => false,
                    'class'     => 'input-file',
                    'value'     => $affiliate->$getSectionLibrary(),
                    'element_type'  => 'file',
                    'upload_dir'    => 'affiliate',
                    'after_element_html' => ($affiliate->$getSectionLibrary()) ? '<strong>'.__('Using').':</strong> '.basename($affiliate->$getSectionLibrary()) : '',
                    'tabindex' => 1,
                ];
                
                $this->_fieldsData[$sKey.'_code'] = [
                    'name'      => 'section_'.$sKey.'_code',
                    'label'     => __('Code'),
                    'required'  => false,
                    'value'     => $affiliate->$getSectionCode(),
                    'element_type'  => 'textarea',
                ];
                
                $this->_fieldsData[$sKey.'_includeon_id'] = [
                    'name'      => 'section_'.$sKey.'_includeon_id',
                    'label'     => __('Execute On'),
                    'required'  => false,
                    'value'     => $affiliate->$getSectionIncludeonId(),
                    'values'    => $includeonValues,
                    'element_type'  => 'select',
                ];
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();
        
        $this->_setFieldsData();

        if (!empty($this->_getSections()) && count($this->_getSections())) {
            foreach ($this->_getSections() as $section) {
                $sKey = $section['key'];
                $fieldset = $form->addFieldset('affiliate_system_'.$sKey, ['legend' => $section['lable'], 'class' => 'fieldset-wide']);
                $fields = ['library', 'code', 'includeon_id'];
                foreach ($fields as $field) {
                    $fieldset->addField('section_'.$sKey.'_'.$field, $this->_fieldsData[$sKey.'_'.$field]['element_type'], $this->_fieldsData[$sKey.'_'.$field]);
                    if ($field == 'library' && $this->_fieldsData[$sKey.'_'.$field]['value']) {
                        
                        $fieldset->addField(
                            'section_'.$sKey.'_library_delete',
                            'checkbox',
                            [
                                'name'      => 'section_'.$sKey.'_library_delete',
                                'after_element_html' => __('Delete JavaScript Library File'),
                            ]
                        );
                        
                    }
                }
            }
        }
        
    }

}
