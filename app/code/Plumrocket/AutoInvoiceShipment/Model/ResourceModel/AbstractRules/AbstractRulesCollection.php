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

namespace Plumrocket\AutoInvoiceShipment\Model\ResourceModel\AbstractRules;

use Plumrocket\AutoInvoiceShipment\Model\AbstractRules;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class AbstractCollection
 */
abstract class AbstractRulesCollection extends AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Add filter by active
     *
     * @return $this
     */
    public function addEnabledFilter()
    {
        return $this->addFieldToFilter('status', AbstractRules::STATUS_ENABLED);
    }

    /**
     * Add filter by Website
     *
     * @param  null | int | array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        if ($websiteId !== null) {
            if (!is_array($websiteId)) {
                $websiteId = [$websiteId];
            }
            $this->addFieldToFilter('websites', ['finset' => $websiteId]);
        }

        return $this;
    }

    /**
     * Set order by Priority
     *
     * @param string $direction ORDER BY
     * @return $this
     */
    public function addSortByPriority($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('rules_priority', $direction);
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this as $item) {
            /**
             * @var $item AbstractRules
             */
            $item->setStoreId([$item->getStoreId()]);
            $item->setWebsites(explode(',', $item->getWebsites()));
            $item->setCustomerGroups(explode(',', $item->getCustomerGroups()));
        }
        return $this;
    }
}
