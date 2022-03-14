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

namespace Plumrocket\AmpEmail\Block\Component\State;

/**
 * @method null|string getProductsWishlistStatus()
 * @method null|string getStateId()
 * @method null|string getIdPrefix()
 */
class Wishlist extends \Plumrocket\AmpEmailApi\Block\AbstractAmpBlock
{
    /**
     * @return string
     */
    public function getAddToWishListUrl() : string
    {
        return $this->getAmpApiUrl('amp-email-api/V1/product_wishlist_add');
    }
}
