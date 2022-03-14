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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model;

class AffiliateManager
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Initialize model
     * @param \Magento\Framework\ObjectManagerInterface $objectManager [description]
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create affiliate model
     * @param  \Plumrocket\Affiliate\Model\Affiliate $model
     * @param  string $typeId
     * @return \Plumrocket\Affiliate\Model\Affiliate
     */
    public function createAffiliate($model, $typeId = null)
    {
        $type = $model->getType($typeId);
        $typeModel = $this->createAffiliateByParam(null, $type);
        if ($typeModel) {
            return $typeModel->simulateLoad($model);
        }
        return $model;
    }

    /**
     * Create affiliate model by model class or type
     *
     * @param null | string                             $modelClass
     * @param null | \Plumrocket\Affiliate\Model\Type   $type
     * @return \Plumrocket\Affiliate\Model\Affiliate
     */
    public function createAffiliateByParam($modelClass = null, $type = null)
    {
        $modelClassName = $modelClass
            ? $modelClass
            : 'Plumrocket\Affiliate\Model\Affiliate\\' . ucfirst($type->getKey());
        return  $this->objectManager->create($modelClassName);
    }

    /**
     * Get affiliate template block
     * @param  \Plumrocket\Affiliate\Model\Type $type
     * @return \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\AbstractNetwork
     */
    public function getAffiliateTemplateBlock($type)
    {
        return $this->objectManager->get('Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\\' . ucfirst($type->getKey()));
    }

}
