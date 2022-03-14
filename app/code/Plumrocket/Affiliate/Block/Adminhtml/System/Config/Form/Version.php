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
 * @package     Plumrocket Affiliate v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form;

class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    /**
     * Wiki link
     * @var string
     */
    protected $_wikiLink = 'http://wiki.plumrocket.com/wiki/Magento_2_Affiliate_Programs_v2.x_Extension';

    /**
     * Full module name
     * @var string
     */
    protected $_moduleName = 'Affiliate Programs';

    /**
     * Get wiki link
     * @return string 
     */
    public function getWikiLink()
    {
        return $this->_wikiLink;
    }
}
