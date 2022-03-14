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

abstract class AbstractNetwork extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\Affiliate\Model\IncludeonFactory
     */
    protected $_includeonFactory;

    /**
     * @var \Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form\Version
     */
    protected $_versionBlock;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Plumrocket\Affiliate\Model\IncludeonFactory                     $includeonFactory
     * @param \Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form\Version $version
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Affiliate\Model\IncludeonFactory $includeonFactory,
        \Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form\Version $version,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_includeonFactory = $includeonFactory;
        $this->_versionBlock = $version;
    }

    /**
     * Get includeon by key
     * @param  string $key
     * @return  \Plumrocket\Affiliate\Model\IncludeonFactory
     */
    public function getIncludeonByKey($key = 'all')
    {
        return $this->_includeonFactory->create()->load($key, 'key');
    }

    /**
     * Get includeon collection
     * @return  \Plumrocket\Affiliate\Model\IncludeonFactory
     */
    public function getIncludeonCollection()
    {
        return $this->_includeonFactory->create()->getCollection();
    }

    /**
     * Get wiki link
     * @return string
     */
    public function getWikiLink()
    {
        return $this->_versionBlock->getWikiLink();
    }
}
