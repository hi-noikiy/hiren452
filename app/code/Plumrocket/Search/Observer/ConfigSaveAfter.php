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

namespace Plumrocket\Search\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\RequestInterface;
use Plumrocket\Search\Model\System\AttributeManager;

class ConfigSaveAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AttributeManager
     */
    private $attributeManager;

    /**
     * ConfigSaveAfter constructor.
     *
     * @param RequestInterface $request
     * @param AttributeManager $attributeManager
     */
    public function __construct(
        RequestInterface $request,
        AttributeManager $attributeManager
    ) {
        $this->request = $request;
        $this->attributeManager = $attributeManager;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $searhDataPriority = $this->request->getPost('psearch-attributes-change');

        if ($searhDataPriority) {
            $this->attributeManager->setAttributeSearchable($searhDataPriority);
        }

        return;
    }
}