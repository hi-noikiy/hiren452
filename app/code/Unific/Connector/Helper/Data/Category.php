<?php

namespace Unific\Connector\Helper\Data;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $dataObjectConverter;

    // Holds a Product API DATA object
    protected $category;

    protected $returnData = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        parent::__construct($context);

        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     */
    public function setCategory(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        $this->category = $category;
        $this->setCategoryInfo();
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return void
     */
    protected function setCategoryInfo()
    {
        $this->returnData = $this->dataObjectConverter->toFlatArray(
            $this->category,
            [],
            \Magento\Catalog\Api\Data\CategoryInterface::class
        );
    }

    /**
     * @return array
     */
    public function getCategoryInfo()
    {
        return $this->returnData;
    }
}
