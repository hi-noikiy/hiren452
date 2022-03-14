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

namespace Plumrocket\Affiliate\Setup;

/* Uninstall Affiliate Programs */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    protected $_configSectionId = 'praffiliate';
    protected $_tables = [
        'plumrocket_affiliate_affiliate',
        'plumrocket_affiliate_includeon',
        'plumrocket_affiliate_type',
    ];
    protected $_attributes = [\Magento\Catalog\Model\Product::ENTITY => ['affiliate_tradedoubler_groupid']];
    protected $_pathes = ['/app/code/Plumrocket/Affiliate'];
}
