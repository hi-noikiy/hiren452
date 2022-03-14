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

namespace Magezon\ProductPagePdf\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var @param \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\TreeFactory
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\ProductPagePdf\Model\PdfProcessorFactory
     */
    protected $pdfProcessorFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magezon\Core\Helper\Data $coreHelper
     * @param \Magezon\ProductPagePdf\Model\PdfProcessorFactory $pdfProcessorFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\ProductPagePdf\Model\PdfProcessorFactory $pdfProcessorFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->dir = $dir;
        $this->fileSystem = $fileSystem;
        $this->assetRepo = $assetRepo;
        $this->coreHelper = $coreHelper;
        $this->pdfProcessorFactory = $pdfProcessorFactory;
    }

    /**
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result = $this->scopeConfig->getValue(
            'productpagepdf/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * Get status attachments category file
     *
     * @return string|null
     */
    public function isEnableFileCategory()
    {
        return $this->getConfig('attach/fileCategory');
    }

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->getConfig('general/enable');
    }

    /**
     * @return string
     */
    public function getIconSrc()
    {
        $iconLoading = $this->getConfig('button/icon_loading');
        if ($iconLoading && $this->getConfig('button/enable_icon_upload')) {
            return $this->coreHelper->getMediaUrl() . 'banner/' . $iconLoading;
        }
        return $this->getViewFileUrl('Magezon_ProductPagePdf::images/icon_page_pdf.png');
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get type of current product and return boolean
     *
     * @return boolean
     */
    public function isAllowProduct() 
    {   
        $currentProductType = $this->getProduct()->getTypeId();
        $pdfProcessor = $this->pdfProcessorFactory->create();
        if ($pdfProcessor->getProfile()->getId()) {
            $productTypes = json_decode($pdfProcessor->getProfile()->getProductTypes());
            foreach($productTypes as $type) {
                if ($currentProductType == $type || $type == 'all') 
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->dir->getRoot();
    }

    /**
     * @return string
     */
    public function getMediaPath()
    {
        $mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
        return $mediaPath;
    }

    /**
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fieldId, $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->_request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fieldId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        }
    }

    /**
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->getConfig('button/title') ?: __('Print PDF');
    }

    /**
     * @return string
     */
    public function getButtonStyle()
    {
        return $this->getConfig('general/customer_css');
    }

    /**
     * @return int
     */
    public function getButtonDisplayType()
    {
        return $this->getConfig('button/type');
    }
}
