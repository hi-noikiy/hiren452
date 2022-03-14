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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Block\Adminhtml\Log;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * empty
     */
    const EMPTY_RESPONSE = '';

    /**
     * @var \Plumrocket\Smtp\Model\LogFactory
     */
    private $logFactory;

    /**
     * @var null
     */
    private $log = null;

    /**
     * View constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Plumrocket\Smtp\Model\LogFactory     $logFactory
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Plumrocket\Smtp\Model\LogFactory $logFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->logFactory = $logFactory;
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->addButton(
            'back_to_emailgrid',
            $this->getButtonData()
        );

        parent::_construct();
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("window.location='%s'", $this->getUrl('prsmtp/log/index')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * @return \Magento\Backend\Block\Template
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->getEmailSubject());

        return parent::_prepareLayout();
    }

    /**
     * @return \Plumrocket\Smtp\Model\Log|null
     */
    private function getLog()
    {
        if (null !== $this->log) {
            return $this->log;
        }

        if ($id = $this->getRequest()->getParam('id')) {
            $this->log = $this->logFactory->create()->load($id);
        }

        return $this->log;
    }

    /**
     * @return string
     */
    public function getEmailHtml()
    {
        return $this->getLog() ? $this->getLog()->getBody() : self::EMPTY_RESPONSE;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getEmailSubject()
    {
        return $this->getLog() ? $this->getLog()->getSubject() : __('SMTP Email View');
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->getLog() ? $this->getLog()->getEmailTo() : self::EMPTY_RESPONSE;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->getLog() ? $this->getLog()->getEmailFrom() : self::EMPTY_RESPONSE;
    }
}
