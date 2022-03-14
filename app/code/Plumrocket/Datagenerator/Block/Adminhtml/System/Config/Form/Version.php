<?php

namespace Plumrocket\Datagenerator\Block\Adminhtml\System\Config\Form;

class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    protected $_wikiLink = 'http://wiki.plumrocket.com/wiki/Magento_2_Data_Feed_Generator_Extension_v2.x';
    protected $_moduleName = 'Data Feed Generator';

    /**
     * get wiki link
     * @return string 
     */
    public function getWikiLink()
    {
    	return $this->_wikiLink;
    }
}