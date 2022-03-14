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

namespace Plumrocket\Search\Helper;

class Config extends Main
{
    /**
     * @var int
     */
    const CATEGORY_DEPTH = 3;

    /**
     * @var string
     */
    const SECTION_ID = 'prsearch';

    /**
     * @var string
     */
    const GROUP_SEARCH = 'search';

    /**
     * @var string
     */
    const GROUP_PRODUCT_SUGGESTION = 'product_suggestion';

    /**
     * @var string
     */
    const GROUP_KEYWORD_SUGGESTION = 'keyword_suggestion';

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $helperCatalogSearch;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\CatalogSearch\Helper\Data        $helperCatalogSearch
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\CatalogSearch\Helper\Data $helperCatalogSearch
    ) {
        $this->helperCatalogSearch = $helperCatalogSearch;
        parent::__construct($objectManager, $context);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function enabledFilterCategories($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_SEARCH . '/filter_categories_enable', $store);
    }

    /**
     * @param null $store
     * @return array
     */
    public function getFilterCategoriesExclude($store = null)
    {
        return explode(
            ',',
            $this->getConfig(self::SECTION_ID . '/' . self::GROUP_SEARCH . '/filter_categories_exclude', $store)
        );
    }

    /**
     * @return int
     */
    public function getFilterCategoriesDepth()
    {
        return self::CATEGORY_DEPTH;
    }

    /**
     * @param null $store
     * @return int|string
     */
    public function getSearchMinLenght($store = null)
    {
        return $this->helperCatalogSearch->getMinQueryLength($store);
    }

    /**
     * @param null $store
     * @return int
     */
    public function getQueryDelay($store = null)
    {
        return (int)$this->getConfig(self::SECTION_ID . '/' . self::GROUP_SEARCH . '/query_delay', $store);
    }

    /* Product Suggestion Settings */

    /**
     * @param null $store
     * @return mixed
     */
    public function enabledProductSuggestion($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION . '/enable', $store);
    }

    /**
     * @param null $store
     * @return int
     */
    public function getPSCount($store = null)
    {
        return (int)$this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION . '/count', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function showPSThumbs($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION . '/thumbs_show', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function showPSPrice($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION . '/price_show', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function showPSRating($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION . '/rating_show', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function showPSShortDescription($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION
            . '/short_description_show', $store);
    }

    /**
     * @param null $store
     * @return int
     */
    public function getPSShortDescriptionLenght($store = null)
    {
        return (int)$this->getConfig(self::SECTION_ID . '/' . self::GROUP_PRODUCT_SUGGESTION
            . '/short_description_lenght', $store);
    }

    /* Keyword Suggestion Settings */

    /**
     * @param null $store
     * @return mixed
     */
    public function enabledCategorySuggestion($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_KEYWORD_SUGGESTION
            . '/category_enable', $store);
    }

    /**
     * @param null $store
     * @return int
     */
    public function getCategorySuggestionCount($store = null)
    {
        return (int)$this->getConfig(self::SECTION_ID . '/' . self::GROUP_KEYWORD_SUGGESTION
            . '/category_count', $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function enabledTermsSuggestion($store = null)
    {
        return $this->getConfig(self::SECTION_ID . '/' . self::GROUP_KEYWORD_SUGGESTION
            . '/terms_enable', $store);
    }

    /**
     * @param null $store
     * @return int
     */
    public function getTermsSuggestionCount($store = null)
    {
        return (int)$this->getConfig(self::SECTION_ID . '/' . self::GROUP_KEYWORD_SUGGESTION
            . '/terms_count', $store);
    }
}
