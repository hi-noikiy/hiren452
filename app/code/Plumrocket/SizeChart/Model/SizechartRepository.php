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

namespace Plumrocket\SizeChart\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\SizeChart\Api\SizechartRepositoryInterface;
use Plumrocket\SizeChart\Api\Data\SizechartInterface;

class SizechartRepository implements SizechartRepositoryInterface
{
    /**
     * @var \Plumrocket\SizeChart\Model\ResourceModel\Sizechart
     */
    protected $sizechartResource;

    /**
     * @var \Plumrocket\SizeChart\Model\SizechartFactory
     */
    protected $sizechartFactory;

    /**
     * @var array
     */
    private $sizecharts = [];

    /**
     * @param ResourceModel\Sizechart $sizechartResource
     * @param SizechartFactory $sizechartFactory
     */
    public function __construct(
        ResourceModel\Sizechart $sizechartResource,
        SizechartFactory $sizechartFactory
    ) {
        $this->sizechartResource = $sizechartResource;
        $this->sizechartFactory = $sizechartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SizechartInterface $sizechart)
    {
        try {
            $this->sizechartResource->save($sizechart);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The "%1" sizechart was unable to be saved. Please try again.', $sizechart->getId())
            );
        }

        return $sizechart;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($sizechartId)
    {
        /** @var \Plumrocket\SizeChart\Model\Sizechart $sizechart */
        $sizechart = $this->sizechartFactory->create();
        $this->sizechartResource->load($sizechart, $sizechartId);

        if (! $sizechart->getId()()) {
            throw new NoSuchEntityException(
                __('The rule with the "%1" ID wasn\'t found. Verify the ID and try again.', $sizechartId)
            );
        }

        return $sizechart;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SizechartInterface $sizechart)
    {
        try {
            $this->sizechartResource->delete($sizechart);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('The "%1" sizechart couldn\'t be removed.', $sizechart->getId()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sizechartId)
    {
        return $this->delete($this->getById($sizechartId));
    }
}
