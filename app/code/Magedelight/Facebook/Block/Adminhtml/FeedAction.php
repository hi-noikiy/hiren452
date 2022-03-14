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
use Magento\Framework\Url as UrlHelper;

class FeedAction extends \Magento\Backend\Block\Template {
    
    /**
     *
     * @var UrlHelper 
     */
    protected $urlHelper;
    
    /**
     * 
     * @param Context $context
     */
    public function __construct(
        Context $context,
        UrlHelper $urlHelper    
    ) {
        parent::__construct($context);
        $this->storeManager = $context->getStoreManager();
        $this->urlHelper = $urlHelper;
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('feed_action_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Generate Feed'),
                    'class' => 'assign icon-btn primary',
                    'on_click' => 'generateFeed()',
                ))
        );
        parent::_prepareLayout();
    }
    public function getFeedActionButtonHtml()
    {
        return $this->getChildHtml('feed_action_button');
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
    
    public function getFeedUrl()
    {
        $storeId = $this->getStoreParam();
        $urlparams = [ '_scope' => $storeId, 
                       '_nosid' => true,
                        ];
        $baseUrl = $this->urlHelper->getUrl('', $urlparams);
        $feedUrl = $baseUrl . "pub" . "/" . "media" ."/"."fb" ."/"."fbshop.csv";
        return $feedUrl;
    }
    
    public function getProgressUrl()
    {
        //$storeId = $this->getStoreParam();
        $storeId = $this->getDefaultStoreId();
        $urlparams = [ '_scope' => $storeId, 
                       '_nosid' => true,
                        ];
        $progressUrl = $this->urlHelper->getUrl('md_facebook/feedaction/GetProgress', $urlparams);
        return $progressUrl;
    }
}
