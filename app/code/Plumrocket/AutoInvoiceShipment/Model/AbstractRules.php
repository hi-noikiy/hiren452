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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model;

use Magento\Rule\Model\AbstractModel;
use Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\CombineFactory;
use Plumrocket\AutoInvoiceShipment\Model\ResourceModel\AbstractRules\AbstractRulesCollection;

/**
 * Class AbstractRules
 *
 * @method AbstractRules setStoreId($storeId)
 * @method AbstractRules setComment($comment)
 * @method AbstractRules setWebsites(array $websites)
 * @method AbstractRules setCustomerGroups(array $groups)
 * @method AbstractRules setRulesPriority($priority)
 *
 * @method int    getStoreId()
 * @method int    getStatus()
 * @method string getComment()
 * @method int    getCommentToEmail()
 * @method array  getWebsites()
 * @method array  getCustomerGroups()
 * @method int    getRulesPriority()
 *
 * @package Plumrocket_AutoInvoiceShipment
 */
abstract class AbstractRules extends AbstractModel
{
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    const APPEND_COMMENT_TO_EMAIL_NO  = 0;
    const APPEND_COMMENT_TO_EMAIL_YES = 1;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Plumrocket_AutoInvoiceShipment';

    /**
     * @var \Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\CombineFactory
     */
    protected $combineFactory;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $actionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $phpSerializer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\formFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\SerializerInterface $phpSerializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context                        $context,
        \Magento\Framework\Registry                             $registry,
        \Magento\Framework\Data\FormFactory                     $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface    $localeDate,
        CombineFactory                                          $combineFactory,
        \Magento\Rule\Model\Action\CollectionFactory            $actionCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface      $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface              $storeManager,
        \Magento\Framework\Serialize\SerializerInterface        $phpSerializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection = null,
        array                                                   $data = []
    ) {
        $this->combineFactory          = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->scopeConfig             = $scopeConfig;
        $this->storeManager            = $storeManager;
        $this->phpSerializer           = $phpSerializer;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Check if rule is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * @return array statuses
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Validate for Invoice and Shipment
     *
     * @param \Magento\Sales\Model\Order $order
     * @param bool                       $success
     *
     * @return bool
     */
    protected function _preValidate($order, $success)
    {
        // validate Order by Website
        if ($success) {
            $success = in_array(
                $this->storeManager->getStore($order->getStoreId())->getWebsiteId(),
                $this->getWebsites()
            );
        }
        // validate Order by Customer Groups
        if ($success) {
            $success = in_array($order->getCustomerGroupId(), $this->getCustomerGroups());
        }

        return $success;
    }

    /**
     * Check if can add comment to email
     *
     * @param  bool $withoutEmail
     * @return bool
     */
    public function canAddComment($withoutEmail = false)
    {
        return ($withoutEmail || $this->getCommentToEmail()) && trim($this->getComment()) !== '';
    }

    /**
     * @param  null | int | array $websiteId
     * @return AbstractRulesCollection
     */
    abstract public function getActiveRules($websiteId = null);

    /**
     * @return mixed
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * @return mixed
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }
}
