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

class Categories extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    protected $_buttonList;
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
            \Magento\Framework\Registry $registry,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Framework\Json\EncoderInterface $jsonEncoder,
            \Magento\Framework\DB\Helper $resourceHelper,
            \Magento\Backend\Model\Auth\Session $backendSession,
            \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
            array $data = array())
    {
        $this->_buttonList = $buttonList;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $jsonEncoder, $resourceHelper, $backendSession, $data);
    }

    public function getFrontUrl($storeId = null)
    {
        return $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\Url::URL_TYPE_WEB);
    }
}
