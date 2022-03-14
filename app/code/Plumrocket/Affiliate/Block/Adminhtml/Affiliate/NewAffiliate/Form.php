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

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\NewAffiliate;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Hidden types of networks
     */
    protected $_hiddenTypes = [
        8, //Chango
        10, //EbayEnterprise
    ];

    /**
     * @var \Plumrocket\Affiliate\Model\Type\ResourceModel\Collection
     */
    protected $_types;

    /**
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $_affiliateTypeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Plumrocket\Affiliate\Model\TypeFactory $affiliateTypeFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Plumrocket\Affiliate\Model\TypeFactory $affiliateTypeFactory,
        array $data = []
    ) {
        $this->_affiliateTypeFactory = $affiliateTypeFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Plumrocket_Affiliate::affiliate.phtml';
    }

    /**
     * Get all types
     *
     * @return Plumrocket\Affiliate\Model\Type
     */
    public function getTypes()
    {
        if ($this->_types === null) {
            $this->_types = $this->_affiliateTypeFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('id', ['nin' => $this->_hiddenTypes])
                ->setOrder('`order`', 'ASC');
        }
        return $this->_types;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'new_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
