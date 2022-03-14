<?php

namespace Unific\Connector\Helper\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Product
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $dataObjectConverter;
    /**
     * @var ProductInterface
     */
    protected $product;
    /**
     * @var array
     */
    protected $returnData = [];

    protected $mainProductAttributes = [
        'id'           => 'entity_id',
        'name'         => 'name',
        'price'        => 'price',
        'sku'          => 'sku',
        'url_key'      => 'url_key',
        'category_ids' => 'category_ids'
    ];

    /**
     * ProductPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
        $this->setProductInfo();
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return void
     */
    protected function setProductInfo()
    {
        $this->returnData = $this->dataObjectConverter->toFlatArray(
            $this->product,
            [],
            ProductInterface::class
        );

        // make sure converting to flat will not overwrite main product attributes
        foreach ($this->mainProductAttributes as $fieldName => $attributeName) {
            $this->returnData[$fieldName] = $this->product->getData($attributeName);
        }

        $this->returnData['product_url_suffix'] = $this->scopeConfig->getValue(
            ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $this->product->getStoreId()
        );
    }

    /**
     * @return array
     */
    public function getProductInfo()
    {
        return $this->returnData;
    }
}
