<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Model\System\Config\Source;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var int
     */
    const MODEL_ID = 1;

    /**
     * @var int
     */
    const DEFAULT_ROOT_CATEGORY_ID = 1;

    /**
     * @var int
     */
    const DEFAULT_STORE_ID = 1;

    /**
     * @var null
     */
    private $options = null;

    /**
     * @var bool
     */
    private $skip = false;

    /**
     * @var string
     */
    private $depthStr = '';

    /**
     * @var \Plumrocket\Search\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * Categories constructor.
     *
     * @param \Plumrocket\Search\Helper\Config           $configHelper
     * @param \Magento\Framework\App\Request\Http        $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory     $categoryFactory
     */
    public function __construct(
        \Plumrocket\Search\Helper\Config $configHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->configHelper = $configHelper;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = [];

        foreach ($this->getOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    /**
     * @return array|null
     */
    private function getOptions()
    {
        if (null === $this->options) {
            $options = [];
            $this->getCategories(null, $options);

            //if (! $this->getSkip()) {
                $options = array_merge(
                    [
                        [
                            'style' => '',
                            'value' => 0,
                            'label' => ' '
                        ]
                    ],
                    $options
                );
            //}

            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * @param null  $categories
     * @param array $options
     * @param int   $level
     */
    private function getCategories($categories = null, &$options = [], $level = 0)
    {
        $configHelper = $this->configHelper;
        $exclude = $configHelper->getFilterCategoriesExclude();

        $depth = $configHelper->getFilterCategoriesDepth();

        if (! $depth || $level >= $depth) {
            return;
        }

        if (null === $categories) {
            $byRequest = ! $this->getSkip();
            $request = $this->request;
            $storeManager = $this->storeManager;
            $websiteCode = $request->getParam('website');

            if ($byRequest) {
                if ($storeCode = $request->getParam('store')) {
                    $defaultStoreId = $storeCode;
                } elseif ($websiteCode) {
                    if ($website = $storeManager->getWebsite($websiteCode)) {
                        $rootCategoryId = [];

                        foreach ($website->getStores() as $store) {
                            $rootCategoryId[] = $store->getRootCategoryId();
                        }

                        $rootCategoryId = array_unique($rootCategoryId);
                    }
                } else {
                    $rootCategoryId = self::DEFAULT_ROOT_CATEGORY_ID;
                }
            } else {
                $defaultStoreId = $storeManager->getWebsite($byRequest ? $websiteCode : null)
                    ->getDefaultGroup()
                    ->getDefaultStoreId();

                if (! $defaultStoreId) {
                    $websites = $storeManager->getWebsites(true);

                    if (! empty($websites[1])) {
                        $defaultStoreId = $websites[1]
                            ->getDefaultGroup()
                            ->getDefaultStoreId();
                    }
                }

                if (! $defaultStoreId) {
                    $defaultStoreId = self::DEFAULT_STORE_ID;
                }
            }

            /** @var \Magento\Catalog\Model\Category $categoryModel */
            $categoryModel = $this->categoryFactory->create();

            if (! empty($rootCategoryId) && is_array($rootCategoryId)) {
                /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
                $categories = $categoryModel->load(self::MODEL_ID)->getChildrenCategories()->addIdFilter($rootCategoryId);
            } elseif (! empty($rootCategoryId)) {
                $categories = $categoryModel->load($rootCategoryId)->getChildrenCategories();
            } elseif (! empty($defaultStoreId)) {
                $rootCategoryId = $this->storeManager->getStore($defaultStoreId)->getRootCategoryId();
                $categories = $categoryModel->load($rootCategoryId)->getChildrenCategories();
            }
        }

        if (is_object($categories)) {
            $categories->addAttributeToFilter('level', $this->getLevelsForSort())
                ->addAttributeToSort('position', 'ASC');
        } else {
            $categories = array_filter($categories, function ($category) {
                return in_array($category->getLevel(), $this->getLevelsForSort(), false);
            });

            usort($categories, static function ($a, $b) {
                return strcmp($a->getPosition(), $b->getPosition());
            });
        }

        foreach ($categories as $category) {
            if ($this->getSkip() && in_array($category->getId(), $exclude)) {
                continue;
            }

            if ($level >= 0) {
                $options[] =
                    [
                        'style' => 'padding-left: ' . (3 + 20 * $level) . 'px;',
                        'value' => $category->getId(),
                        'label' => str_repeat($this->depthStr, $level) . $category->getName()
                    ];
            }

            if ($category->hasChildren()) {
                $children = $category->getChildren();

                if (is_string($children)) {
                    $children = $category
                        ->getCollection()
                        ->addAttributeToSelect(['name'])
                        ->addIdFilter($children);
                }

                $this->getCategories($children, $options, $level + 1);
            }
        }
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setSkip($flag = true)
    {
        $this->skip = (bool)$flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @param $str
     * @return $this
     */
    public function setDepthStr($str)
    {
        $this->depthStr = (string)$str;

        return $this;
    }

    /**
     * @return array
     */
    private function getLevelsForSort()
    {
        return [1, 2, 3];
    }
}
