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
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Model;

use Plumrocket\SizeChart\Api\Data\SizechartInterface;

class Sizechart extends \Magento\Rule\Model\AbstractModel implements SizechartInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Plumrocket_SizeChart';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'sizechart';

    /**
     * Check if conditions was erased
     *
     * @var bool
     */
    protected $_eraseConditions = false;

    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory $combineFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory $combineFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\SizeChart\Model\ResourceModel\Sizechart::class);
    }

    public function isEnabled()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    public function getAvailableDisplayTypes()
    {
        return [__('In Popup'), self::STATUS_ENABLED => __('On Page')];
    }

    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    public function beforeSave()
    {
        if ($this->getConditions()) {
            $conditions = $this->getConditions()->asArray();

            if (isset($conditions['conditions']) && empty($conditions['conditions'][0])) {
                $this->_eraseConditions = true;
                $this->setConditionsSerialized('');
                $this->unsConditions();
            }
        }

        return parent::beforeSave();
    }

    public function getConditions()
    {
        if ($this->_eraseConditions) {
            return null;
        }

        return parent::getConditions();
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getButtonLabel()
    {
        return $this->getData(self::BUTTON_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getDisplayType()
    {
        return $this->getData(self::DISPLAY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getConditionsIsMain()
    {
        return $this->getData(self::CONDITIONS_IS_MAIN);
    }

    /**
     * @inheritDoc
     */
    public function getConditionsPriority()
    {
        return $this->getData(self::CONDITIONS_PRIORITY);
    }

    /**
     * @inheritDoc
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setButtonLabel($buttonLabel)
    {
        $this->setData(self::BUTTON_LABEL, $buttonLabel);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->setData(self::CONTENT, $content);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDisplayType($displayType)
    {
        $this->setData(self::DISPLAY_TYPE, $displayType);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConditionsIsMain($conditionsIsMain)
    {
        $this->setData(self::CONDITIONS_IS_MAIN, $conditionsIsMain);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConditionsPriority($conditionsPriority)
    {
        $this->setData(self::CONDITIONS_PRIORITY, $conditionsPriority);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);
        return $this;
    }
}
