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

namespace Plumrocket\Affiliate\Block;

class Section extends \Magento\Framework\View\Element\Template
{
    const INCLUDEON_RKEY = 'affiliate_script_includeon';

    /**
     * @var string
     */
    protected $_section;

    /**
     * @var Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @param \Plumrocket\Affiliate\Helper\Data                $dataHelper
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {

        if (!$this->_dataHelper->moduleEnabled()) {
            return;
        }

        $_section = $this->getSection();

        $getSectionIncludeonId = 'getSection'.ucfirst($_section).'IncludeonId';

        if ($this->_customerSession->getPlumrocketAffiliateRegisterSuccess()) {
            $this->addIncludeon('registration_success_pages');
            if ($_section == \Plumrocket\Affiliate\Model\Affiliate\AbstractModel::SECTION_BODYEND) {
                $this->_customerSession->setPlumrocketAffiliateRegisterSuccess(false);
            }
        }
        if ($this->_customerSession->getPlumrocketAffiliateLoginSuccess()) {
            $this->addIncludeon('login_success_pages');
            if ($_section == \Plumrocket\Affiliate\Model\Affiliate\AbstractModel::SECTION_BODYEND) {
                $this->_customerSession->setPlumrocketAffiliateLoginSuccess(false);
            }
        }

        $html = '';

        foreach ($this->_dataHelper->getPageAffiliates() as $affiliate) {

            $_includeon = $this->_dataHelper->getIncludeon($affiliate->$getSectionIncludeonId());

            if (!$_includeon) {
                continue;
            }

            if (!$this->inIncludeon($_includeon->getKey())) {
                continue;
            }

            $html .= $affiliate->getLibraryHtml($_section, $this->getIncludeon());
            $html .= $affiliate->getCodeHtml($_section, $this->getIncludeon());

        }

        return $html;
    }

    /**
     * Set section
     * @param string $section
     * @return  $this
     */
    public function setSection($section)
    {
        $this->_section = $section;
        return $this;
    }

    /**
     * Get section
     * @return string
     */
    public function getSection()
    {
        return $this->_section;
    }

    /**
     * Add includeon
     * @param string $section
     * @return $this
     */
    public function addIncludeon($section)
    {
        $includeon = $this->getIncludeon();
        if (!isset($includeon[$section])) {
            $includeon[$section] = $section;
            $this->setIncludeon($includeon);
        }
        return $this;
    }

    /**
     * Set includeon
     * @param object $includeon
     * @return  string
     */
    public function setIncludeon($includeon)
    {
        $this->_registry->unregister(self::INCLUDEON_RKEY);
        $this->_registry->register(self::INCLUDEON_RKEY, $includeon);
        return $this;
    }
    
    /**
     * Isseet includeon
     * @param  string $value
     * @return boolean
     */
    public function inIncludeon($value)
    {
        return in_array($value, $this->getIncludeon());
    }

    /**
     * Get includeon
     * @return [type] [description]
     */
    public function getIncludeon()
    {
        $result = $this->_registry->registry(self::INCLUDEON_RKEY);
        if (!$result) {
            $result = ['all' => 'all'];
        }
        return $result;
    }
}
