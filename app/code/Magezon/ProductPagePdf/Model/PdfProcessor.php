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

namespace Magezon\ProductPagePdf\Model;

class PdfProcessor
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var \Magento\Email\Model\Template\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var \Magezon\ProductPagePdf\Model\Profile
     */
    protected $profile;

    /**
     * @var \Magezon\ProductPagePdf\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var boolean
     */
    protected $enableStoreEmulation = true;

    /**
     * @var string
     */
    protected $style;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magezon\Core\Helper\Data $coreHelper
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Email\Model\Template\FilterFactory $filterFactory
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magezon\Builder\Helper\Data $builderHelper
     * @param \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magezon\ProductPagePdf\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Email\Model\Template\FilterFactory $filterFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magezon\ProductPagePdf\Helper\Data $dataHelper
    ) {
        $this->registry = $registry;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->coreHelper = $coreHelper;
        $this->fileSystem = $fileSystem;
        $this->appEmulation = $appEmulation;
        $this->filterFactory = $filterFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->builderHelper = $builderHelper;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magezon\ProductPagePdf\Model\Profile
     * @return array
     */
    public function getSettingPdf($profile)
    {
        $options = [];
        $options['orientation'] = $profile->getPdfOrientation();
        $options['format'] = $profile->getPdfPageSize();
        if (!$this->coreHelper->isNull($profile->getPdfMarginTop())) {
            $options['margin_top'] = $this->getOption($profile->getPdfMarginTop());
        }
        if (!$this->coreHelper->isNull($profile->getPdfMarginRight())) {
            $options['margin_right'] = $this->getOption($profile->getPdfMarginRight());
        }
        if (!$this->coreHelper->isNull($profile->getPdfMarginBottom())) {
            $options['margin_bottom'] = $this->getOption($profile->getPdfMarginBottom());
        }
        if (!$this->coreHelper->isNull($profile->getPdfMarginLeft())) {
            $options['margin_left'] = $this->getOption($profile->getPdfMarginLeft());
        }

        return $options;
    }

    /**
     * @param int $value
     * @return int|void
     */
    public function getOption ($value) {
        if ($value < 250 && $value >= 0) {
            return (float)$value;
        }
    }

    /**
     * @param int $id
     * @return \Mpdf\Mpdf
     */
    public function getProductPdf($id)
    {
        $product = $this->productFactory->create();
        $product->load($id);

        
        if ($product->getId()) {
            $this->setProduct($product);
            $profile = $this->getProfile();
            $options = $this->getSettingPdf($profile);
            if ($profile->getId()) {
                return $this->getPdf($profile->getProfile(), $options);
            }
        }
    }

    /**
     * @param string $profile
     * @param array $options
     * @return \Mpdf\Mpdf
     */
    public function getPdf($profile, $options = [])
    {
        $varDirectory = $this->fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::TMP);
        $config = [
            'mode'              => 'c',
            'tempDir'           => $varDirectory->getAbsolutePath(),
            'format'            => 'A4',
            'margin_header'     => 3,
            'margin_footer'     => 3
        ];

        if ($options) {
            $config = array_replace_recursive($config, $options);
        }

        try {
            $mpdf = new \Mpdf\Mpdf($config);

            // Code changed by @JiteshBagul

            // $mpdf = new \mPDF('utf-8','A4','');

            // print_r($mpdf);
            // exit();
            // $mpdf = new \mPDF($config);
            
            $this->preparFooter($mpdf);
            $mpdf->WriteHTML($this->getStyle(), \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($this->getHtml($profile));

            return $mpdf;
        } catch (\Mpdf\MpdfException $e) {
            throw new \Exception(__('Creating an mPDF object failed with %1', $e->getMessage()));
        }
    }

    /**
     * @param \Mpdf\Mpdf $mpdf
     * @return void
     */
    public function preparFooter($mpdf) {
        $enableFooter = $this->dataHelper->getConfig('footer/enabled');
        if ($enableFooter) {
            $footer = $this->getFooterHtml();
            $mpdf->defaultfooterline = 0;
            $mpdf->setFooter($footer);
        } 
    }

    /**
     * @param string $profile
     * @return string
     */
    public function getHtml($profile)
    {
        if ($this->getEnableStoreEmulation()) {
            $this->appEmulation->startEnvironmentEmulation($this->getStore()->getId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);
        }
        $block = $this->builderHelper->prepareProfileBlock(\Magezon\SimpleBuilder\Block\Profile::class, $profile);
        $html = $block->toHtml();
        $filter = $this->filterFactory->create();

        $product = $this->getProduct();
        $product->setUrl($product->getProductUrl());
        $variables = [
            'product' => $product
        ];
        $filter->setVariables($variables);
        $result = html_entity_decode($filter->filter($html));
        if ($this->getEnableStoreEmulation()) {
            $this->appEmulation->stopEnvironmentEmulation();
        }
        return $result;
    }

    /**
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function getProfile()
    {
        if ($this->profile == null) {
            $ids = $this->getProfilesIds();
            $collection = $this->profileCollectionFactory->create();
            $collection->prepareCollection()
                ->addFieldToFilter('main_table.profile_id', ['in' => $ids])
                ->setOrder('priority','DESC');
            $profile = $collection->getFirstItem();

            if (!$profile->getId() && $default = $this->getDefaultProfile()) {
                $profile = $default;
            }
            $this->profile = $profile;
        }
        return $this->profile;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magezon\ProductPagePdf\Model\PdfProcessor
     */
    public function setProduct($product)
    {
        $this->registry->register('current_product', $product);
        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Retrieve application store object
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return array
     */
    public function getProfilesIds()
    {
        $profilesRelations = $this->getProfilesRelations();
        $ids = [];
        foreach ($profilesRelations as $_re) {
            if (!in_array($_re['profile_id'], $ids)) {
                $ids[] = $_re['profile_id'];
            }
        }
        return $ids;
    }

    /**
     * @return array
     */
    public function getProfilesRelations()
    {
        $store  = $this->getStore();
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tableName  = $connection->getTableName('mgz_productpagepdf_profile_product');
        $select     = $connection->select()->from($tableName)
                        ->where('product_id = ?', $this->getProductId())
                        ->where('store_id = ?', $store->getId());

        return (array) $connection->fetchAll($select);
    }

    /**
     * @return \Magezon\ProductPagePdf\Model\Profile
     */
    public function getDefaultProfile()
    {
        $defaultProfileId = $this->dataHelper->getConfig('general/default_profile');
        if ($defaultProfileId) {
            $collection = $this->profileCollectionFactory->create();
            $collection->addFieldToFilter('profile_id', $defaultProfileId);
            return $collection->getFirstItem();
        }
    }

    /**
     * @param boolean $enableStoreEmulation
     */
    public function setEnableStoreEmulation($enableStoreEmulation)
    {
        $this->enableStoreEmulation = $enableStoreEmulation;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableStoreEmulation()
    {
        return $this->enableStoreEmulation;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        $productName = $this->getProduct()->getName();
        $name = str_replace(' ', '_', $productName);
        return $name . '.pdf';
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        $profile = $this->getProfile();
        $destination = \Mpdf\Output\Destination::INLINE;
        if ($profile->getAutoDownload()) {
            $destination = \Mpdf\Output\Destination::DOWNLOAD;
        }
        return $destination;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        if ($this->style == null) {
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $cssPath = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::MODULE, 'Magezon_ProductPagePdf') . '/css/product_page_pdf.css';
            $this->style = file_get_contents($cssPath);
        }
        return $this->style;
    }

    /**
     * @param int $position
     * @param string $replace
     * @param string $html
     * @return string
     */
    public function updateFooterHtml($position, $replace, $html)
    {
        $key = 'foot_right';
        switch ($position) {
            case 0:
                $key = 'foot_left';
                break;
            case 1:
                $key = 'foot_center';
                break;
        }
        return str_replace($key, $replace, $html);
    }

    /**
     * @return string
     */
    public function getFooterHtml()
    {
        $enabledNumbering       = $this->dataHelper->getConfig('footer/enable_numbering');
        $enabledCurrentDate     = $this->dataHelper->getConfig('footer/enable_current_date');
        $enabledTextFoot        = $this->dataHelper->getConfig('footer/enable_text_foot');
        $positionNumbering      = $this->dataHelper->getConfig('footer/numbering_position');
        $positionCurrentDate    = $this->dataHelper->getConfig('footer/current_date_position');
        $positionText           = 1;
        $textFooter             = $this->dataHelper->getConfig('footer/text_footer');
        $currentDate            = date("d/m/Y");
        $numberPage             = '{PAGENO} / {nb}';

        for ($i = 0; $i < 3; $i++) {
            if ($positionNumbering != $i && $positionCurrentDate != $i) {
                $positionText = $i;
                break;
            }
        }

        $html = 'foot_left | foot_center | foot_right';
        if ($enabledNumbering) {
            $html = $this->updateFooterHtml($positionNumbering, $numberPage, $html);
        }
        if ($enabledCurrentDate) {
            $html = $this->updateFooterHtml($positionCurrentDate, $currentDate, $html);
        }
        if ($enabledTextFoot) {
            $html = $this->updateFooterHtml($positionText, $textFooter, $html);
        }
        $html = str_replace(['foot_left','foot_center','foot_right'], ' ', $html);
        
        return $html;
    }
}
