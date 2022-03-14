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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\AmpEmail\Block\Component\ProductAlert;

/**
 * @method getProductTemplate()
 * @method getProductShowButtons()
 * @method getProductShowAttributes()
 * @method getProductVersion()
 */
class Stock extends \Plumrocket\AmpEmailApi\Block\AbstractComponent
{
    /**
     * @var \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface
     */
    private $componentProductLocator;

    /**
     * @var string
     */
    protected $styleFileId = 'Plumrocket_AmpEmail::css/component/v1/product_alert/stock.css';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * Stock constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param \Magento\Framework\Url                                    $frontUrlBuilder
     * @param \Plumrocket\AmpEmailApi\Api\ComponentDataLocatorInterface $componentDataLocator
     * @param \Magento\Framework\View\Asset\Repository                  $viewAssetRepository
     * @param \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface $componentProductLocator
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface      $localeDate
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url $frontUrlBuilder,
        \Plumrocket\AmpEmailApi\Api\ComponentDataLocatorInterface $componentDataLocator,
        \Magento\Framework\View\Asset\Repository $viewAssetRepository,
        \Plumrocket\AmpEmail\Api\ComponentProductLocatorInterface $componentProductLocator,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $data = []
    ) {
        parent::__construct($context, $frontUrlBuilder, $componentDataLocator, $viewAssetRepository, $data);
        $this->componentProductLocator = $componentProductLocator;
        $this->localeDate = $localeDate;
    }

    /**
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts() : array
    {
        return $this->componentProductLocator->getProducts();
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Plumrocket\AmpEmail\Block\Component\Product\View
     */
    public function createProductBlock(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        /** @var \Plumrocket\AmpEmail\Block\Component\Product\View $component */
        $component = $this->getLayout()->createBlock(\Plumrocket\AmpEmail\Block\Component\Product\View::class);
        $component->setData(
            [
                'product'         => $product,
                'show_attributes' => $this->getProductShowAttributes(),
                'show_buttons'    => $this->getProductShowButtons(),
                'version'         => $this->getProductVersion(),
            ]
        );
        $component->setTemplate($this->getProductTemplate());
        $component->setComponentPartsCollector($this->getComponentPartsCollector());

        return $component;
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getActualStockUrl(int $productId) : string
    {
        return $this->getAmpApiUrl('amp-email-api/V1/productAlert_actual_stock', ['product' => $productId]);
    }

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId) : string
    {
        return $this->getAmpApiUrl(
            'amp-email-api/V1/productAlert_unsubscribe_stock',
            ['product' => $productId]
        );
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl() : string
    {
        return $this->getAmpApiUrl('amp-email-api/V1/productAlert_unsubscribe_stockAll');
    }

    /**
     * @return string
     */
    public function getCurrentDate() : string
    {
        return $this->localeDate->formatDateTime(
            null,
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL
        );
    }
}
