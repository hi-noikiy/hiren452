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

namespace Plumrocket\AmpEmail\Block\Component\Product;

use Plumrocket\AmpEmailApi\Block\ProductListComponentInterface;

/**
 * Class Bestsellers
 * Used for rendering amp list with bestsellers
 *
 * @method getPeriod()
 * @method getProductsCount()
 */
class Bestsellers extends \Plumrocket\AmpEmailApi\Block\AbstractProductComponent implements
    ProductListComponentInterface
{
    /**
     * @var string
     */
    protected $styleFileId = 'Plumrocket_AmpEmail::css/component/:version/product/amp-carousel.css';

    /**
     * @return string
     */
    public function getListUrl() : string
    {
        return $this->getAmpApiUrl(
            'amp-email-api/V1/product_bestseller',
            [
                'period' => $this->getPeriod(),
                'count' => $this->getProductsCount(),
            ]
        );
    }
}
