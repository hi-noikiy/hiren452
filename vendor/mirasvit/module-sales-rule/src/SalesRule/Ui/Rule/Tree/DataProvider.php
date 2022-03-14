<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Ui\Rule\Tree;

use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Model\Config\Source\Group as GroupSource;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\Rule\Metadata\ValueProvider as RuleValueProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var RuleCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GroupSource
     */
    private $groupSource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlManager;

    /**
     * @var RuleValueProvider
     */
    private $ruleValueProvider;

    /**
     * DataProvider constructor.
     * @param RuleCollectionFactory $collectionFactory
     * @param GroupSource $groupSource
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlManager
     * @param RuleValueProvider $ruleValueProvider
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        RuleCollectionFactory $collectionFactory,
        GroupSource $groupSource,
        StoreManagerInterface $storeManager,
        UrlInterface $urlManager,
        RuleValueProvider $ruleValueProvider,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collection        = $collectionFactory->create();
        $this->groupSource       = $groupSource;
        $this->storeManager      = $storeManager;
        $this->urlManager        = $urlManager;
        $this->ruleValueProvider = $ruleValueProvider;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return mixed
     */
    public function getConfigData()
    {
        $data = parent::getConfigData();

        $collection = $this->collectionFactory->create()
            ->setOrder('is_active', 'desc')
            ->setOrder('sort_order', 'desc');

        $rules = [];

        /** @var \Magento\SalesRule\Model\Rule $rule */
        foreach ($collection as $rule) {
            $isActive = $rule->getIsActive() ? true : false;

            if ($rule->getFromDate() && strtotime($rule->getFromDate()) > time()) {
                $isActive = false;
            }

            if ($rule->getToDate() && strtotime($rule->getToDate()) < time()) {
                $isActive = false;
            }

            $coupon = '-';

            if ($rule->getCouponType() == 2) {
                if ($rule->getUseAutoGeneration()) {
                    $coupon = __('Use Auto Generation');
                } else {
                    $coupon = $rule->getPrimaryCoupon()->getCode();
                }
            }

            $rules[] = [
                'name'                => $rule->getName(),
                'isActive'            => $isActive,
                'stopRuleProcessing'  => (int)$rule->getStopRulesProcessing(),
                'fromDate'            => $rule->getFromDate(),
                'toDate'              => $rule->getToDate(),
                'sortOrder'           => $rule->getSortOrder(),
                'conditions'          => $rule->getConditions()->asStringRecursive(),
                'actions'             => $rule->getActions()->asStringRecursive(),
                'typeLabel'           => $this->getTypeLabel($rule),
                'discountAmount'      => $rule->getDiscountAmount(),
                'customerGroupLabels' => implode(', ', $this->getCustomerGroupLabels($rule)),
                'websiteLabels'       => implode(', ', $this->getWebsiteLabels($rule)),
                'couponType'          => $rule->getCouponType(),
                'coupon'              => $coupon,
                'edit'                => $this->urlManager->getUrl('sales_rule/promo_quote/edit', ['id' => $rule->getId()]),
                'children'            => [],
            ];
        }

        usort($rules, function ($a, $b) {
            return $b['isActive'];
        });

        $data['rules'] = $this->buildTree($rules, 0);

        foreach ($rules as $rule) {
            if (!$rule['isActive']) {
                $data['rules'][] = $rule;
            }
        }

        return $data;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return array
     */
    private function getCustomerGroupLabels($rule)
    {
        $labels = [];

        foreach ($rule->getCustomerGroupIds() as $id) {
            foreach ($this->groupSource->toOptionArray() as $option) {
                if ((int)$option['value'] == (int)$id) {
                    if ((int)$id === 0) {
                        $option['label'] = (string)__('NOT LOGGED IN');
                    }
                    $labels[] = $option['label'];
                }
            }
        }

        return $labels;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return array
     */
    private function getWebsiteLabels($rule)
    {
        $labels = [];

        foreach ($rule->getWebsiteIds() as $id) {
            foreach ($this->storeManager->getWebsites() as $website) {
                if ($website->getId() == (int)$id) {
                    $labels[] = $website->getName();
                }
            }
        }

        return $labels;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return string
     */
    private function getTypeLabel($rule)
    {
        $meta = $this->ruleValueProvider->getMetadataValues($rule);
        foreach ($meta['actions']['children']['simple_action']['arguments']['data']['config']['options'] as $option) {
            if ($option['value'] == $rule->getSimpleAction()) {
                return $option['label'];
            }
        }

        return '';
    }

    /**
     * @param array $rules
     * @param int $from
     * @return array
     */
    private function buildTree($rules, $from)
    {
        $set = [];
        foreach ($rules as $idx => $rule) {
            if ($from > $idx || !$rule['isActive']) {
                continue;
            }

            if ($rule['stopRuleProcessing']) {
                $rule['children'] = $this->buildTree($rules, $idx + 1);
                $set[]            = $rule;
                break;
            }

            $set[] = $rule;
        }

        return $set;
    }
}