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
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\SizeChart\Api\SizechartRepositoryInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart
     */
    protected $sizechartCollection;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $jsonSerializer;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var SizechartRepositoryInterface
     */
    protected $sizechartRepository;

    /**
     * UpgradeData constructor.
     *
     * @param \Plumrocket\SizeChart\Model\ResourceModel\Sizechart\Collection $sizechartCollection
     * @param SizechartRepositoryInterface $sizechartRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $jsonSerializer
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Plumrocket\SizeChart\Model\ResourceModel\Sizechart\Collection $sizechartCollection,
        \Plumrocket\SizeChart\Api\SizechartRepositoryInterface $sizechartRepository,
        \Magento\Framework\Serialize\SerializerInterface $jsonSerializer,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->sizechartCollection = $sizechartCollection;
        $this->jsonSerializer = $jsonSerializer;
        $this->serializer = $serializer;
        $this->sizechartRepository = $sizechartRepository;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.2.9', '<=')) {
            $sizechartItems = $this->sizechartCollection->getItems();

            /** @var \Plumrocket\SizeChart\Api\Data\SizechartInterface $item */
            foreach ($sizechartItems as $item) {
                try {
                    if(!empty($item->getConditionsSerialized())){
                        $this->jsonSerializer->unserialize($item->getConditionsSerialized());
                    }
                } catch (\Exception $e) {
                    $conditionsSerialized = $this->serializer->unserialize($item->getConditionsSerialized());
                    $item->setConditionsSerialized($this->jsonSerializer->serialize($conditionsSerialized));
                    $this->sizechartRepository->save($item);
                }
            }

        }
    }
}
