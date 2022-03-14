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

namespace Plumrocket\SizeChart\Api;

interface SizechartRepositoryInterface
{
    /**
     * @param \Plumrocket\SizeChart\Api\Data\SizechartInterface $sizechart
     * @return \Plumrocket\SizeChart\Api\Data\SizechartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Plumrocket\SizeChart\Api\Data\SizechartInterface $sizechart);

    /**
     * @param int $sizechartId
     * @return \Plumrocket\SizeChart\Api\Data\SizechartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($sizechartId);

    /**
     * @param \Plumrocket\SizeChart\Api\Data\SizechartInterface $sizechart
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Plumrocket\SizeChart\Api\Data\SizechartInterface $sizechart);

    /**
     * @param int $sizechartId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($sizechartId);
}
