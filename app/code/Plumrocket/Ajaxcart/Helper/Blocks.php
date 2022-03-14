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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Helper;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Helper\Product\View as ProductViewHelper;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Blocks extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Plumrocket\Ajaxcart\Controller\AbstractCart
     */
    protected $controller = null;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $productViewHelper;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    protected $dataHelper;

    /**
     * Description
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Helper\Product\View $productViewHelper
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Plumrocket\Ajaxcart\Helper\Data $dataHelper
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        ProductHelper $productHelper,
        ProductViewHelper $productViewHelper,
        CatalogSession $catalogSession,
        MessageManager $messageManager,
        Data $dataHelper,
        Context $context
    ) {
        $this->productHelper = $productHelper;
        $this->productViewHelper = $productViewHelper;
        $this->catalogSession = $catalogSession;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * setController
     * @param \Plumrocket\Ajaxcart\Controller\AbstractCart
     * @return type|object $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param string $blockName
     * @param string $handle
     * @param bool $loadLayout
     * @return array $result
     */
    public function getBlocksHtml($blockName, $handle = null, $loadLayout = true)
    {
        $result = [];
        if (empty($blockName) || !$this->dataHelper->moduleEnabled()) {
            return $result;
        }

        $blockNames = is_array($blockName) ? $blockName : [$blockName];

        $view = $this->controller->getView();

        if ($handle) {
            $update = $view->getLayout()->getUpdate();
            $update->addHandle('default');
            $view->addActionLayoutHandles();
            $update->addHandle($handle);
            $view->loadLayoutUpdates();
            $view->generateLayoutXml();
            $view->generateLayoutBlocks();
        } else {
            if ($loadLayout) {
                $view->loadLayout();
            }
        }

        foreach($blockNames as $key => $name) {
            if ($block = $view->getLayout()->getBlock($name)) {
                $result[$name] = $block->toHtml();
            } else {
                $this->messageManager->addNotice(__('Can not get "%1" block', $name));
            }
        }

        return $result;
    }

    /**
     * Description
     * @param type $productId
     * @param type|null $params
     * @return type
     */
    public function prepareProductLayout($productId, $params = null)
    {
        $controller = $this->controller;
        // Prepare data
        $productHelper = $this->productHelper;
        if (!$params) {
            $params = new \Magento\Framework\DataObject();
        }

        // Standard algorithm to prepare and render product view page
        $product = $productHelper->initProduct($productId, $controller, $params);
        if (!$product) {
            throw new NoSuchEntityException(__('Product is not loaded'));
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        $this->_eventManager->dispatch('catalog_controller_product_view', ['product' => $product]);

        if ($params->getSpecifyOptions()) {
            $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
            $this->messageManager->addNotice->addNotice($notice);
        }

        $this->catalogSession->setLastViewedProductId($product->getId());
        $page = $this->controller->getResultPage()->create();
        // if ($product->hasCustomOptions() && !$product->getCustomOption('bundle_selection_ids')) {
        //     $options = $buyRequest ? $buyRequest->getBundleOption() : null;
        //     if (is_array($options)) {
        //         $options = $this->recursiveIntval($options);
        //         $optionIds = array_keys($options);
        //     } else {
        //         $optionIds = $product->getTypeInstance()
        //             ->getOptionsIds($product);
        //     }
        //     $product->addCustomOption('bundle_selection_ids', serialize($selectionIds));
        // }

        $this->productViewHelper->initProductLayout($page, $product, $params);

        return $this;
    }
}
