<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

class Groups implements ArrayInterface
{
    protected $options;

    protected $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->collectionFactory->create()->setRealGroupsFilter()->loadData()->toOptionArray();
            array_unshift($this->options, ['value' => '0', 'label' => __('NOT LOGGED IN')]);
        }

        return $this->options;
    }
}
