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

namespace Magezon\ProductPagePdf\Controller\Adminhtml\Pdf;

class Search extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_ProductPagePdf::profile';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function execute()
    {
        $result['status'] = false;
        $post = $this->getRequest()->getPostValue();
        if (isset($post['s']) && $post['s']) {
            $type = isset($post['type']) ? $post['type'] : 'product';
            $collection = $this->productCollectionFactory->create();
            $collection->addFieldToFilter('name', ['like' => '%' . $post['s'] . '%']);

            $options = [];
            foreach ($collection as $item) {
                $options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getId()
                ];
            }
            $result['status']  = true;
            $result['options'] = $options;
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}
