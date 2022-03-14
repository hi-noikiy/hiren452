<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Cron;

class DailyPagePdfUpdate
{
    /**
     * @var \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @var \Magezon\ProductPagePdf\Model\Processor
     */
    protected $processor;

    /**
     * @param \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magezon\ProductPagePdf\Model\Processor $processor
     */
    public function __construct(
        \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magezon\ProductPagePdf\Model\Processor $processor
    ) {
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->processor = $processor;
    }

    /**
     * @return \Magezon\ProductPagePdf\Model\ResourceModel\Profile\Collection
     */
    public function process()
    {
        $collection = $this->profileCollectionFactory->create();
        if ($collection->count()) {
            foreach ($collection as $item) {
                $this->processor->process($item);
            }
        }
    }
}
