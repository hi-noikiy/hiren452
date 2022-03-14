<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;

class CategoryFeed extends \Magento\Backend\Block\Template {
    
    /**
     * 
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->storeManager = $context->getStoreManager();
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('feed_assign_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Assign'),
                    'class' => 'assign icon-btn primary',
                    'on_click' => 'catFeedRequest(true)',
                ))
        );
        $this->setChild('feed_unassign_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Unassign'),
                    'class' => 'unassign icon-btn primary',
                    'on_click' => 'catFeedRequest(false)',
                ))
        );
        parent::_prepareLayout();
    }
    public function getFeedAssignButtonHtml()
    {
        return $this->getChildHtml('feed_assign_button');
    }

    public function getFeedUnAssignButtonHtml()
    {
        return $this->getChildHtml('feed_unassign_button');
    }
    
    public function getStoreParam()
    {
        return $this->getRequest()->getParam('store', null);
    }
    
    public function getDefaultStoreId()
    {
        foreach ($this->storeManager->getWebsites(false) as $_website):
            if ($_website->getIsDefault()) {
                return $_website->getDefaultStore()->getId();
            }
        endforeach;
    }
}
