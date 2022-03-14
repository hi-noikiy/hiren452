<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Controller\Ajax;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\Search\Helper\Data
     */
    private $helper;

    /**
     * @var \Plumrocket\Search\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $helperCatalogSearch;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Framework\Stdli\StringUtils
     */
    private $string;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Plumrocket\Search\Helper\Data             $helper
     * @param \Plumrocket\Search\Helper\Config           $config
     * @param \Magento\CatalogSearch\Helper\Data         $helperCatalogSearch
     * @param \Magento\Search\Model\QueryFactory         $queryFactory
     * @param \Magento\Framework\Stdlib\StringUtils      $string
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\App\Response\Http       $response
     * @param \Magento\Framework\App\Request\Http        $request
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Search\Helper\Data $helper,
        \Plumrocket\Search\Helper\Config $config,
        \Magento\CatalogSearch\Helper\Data $helperCatalogSearch,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->config = $config;
        $this->helperCatalogSearch = $helperCatalogSearch;
        $this->queryFactory = $queryFactory;
        $this->string = $string;
        $this->response = $response;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $helper = $this->helper;
        $config = $this->config;

        if (! $helper->moduleEnabled()) {
            return;
        }

        $queryText = $helper->getQueryText();

        $data = [
            'success'   => false,
            'content'   => null,
            'q'         => $this->request->getParam('q'),
        ];

        try {
            $query = $this->queryFactory->get();
            $query->setStoreId($this->storeManager->getStore()->getId());

            if ($this->helperCatalogSearch ->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                } else {
                    $query->setPopularity(1);
                }
                $query->save();
            }

            if ($this->string->strlen($queryText) >= $config->getSearchMinLenght()) {
                $this->registry->register("psearch_isajax", true);
                $this->_view->loadLayout();
                $data['content'] = $this->_view->getLayout()
                    ->getBlock('psearch.tooltip')
                    ->toHtml();

                $data['success'] = true;
            }
        } catch (\Exception $e) {
            $data['content'] = $e->getMessage();
        }

        $this->response->setBody($this->jsonHelper->jsonEncode($data));
    }
}

