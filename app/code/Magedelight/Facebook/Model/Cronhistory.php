<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Cronhistory
 */
class Cronhistory extends AbstractModel
{
    const MANUAL = 1;
    const CRON = 2;
    const MANUAl_LABEL = 'Manual';
    const CRON_LABEL = 'Cron';
    const SUCCESS = 1;
    const FAILED = 2;
    const SUCCESS_LABEL = 'Success';
    const FAILED_LABEL = 'Failed';
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Magedelight\Facebook\Model\ResourceModel\Cronhistory');
    }
}