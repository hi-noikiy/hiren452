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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Block\WorkMode;

/**
 * Class Js
 *
 * @package Plumrocket\Ajaxcart\Block\WorkMode
 */
class Js extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Js constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\Helper\Data              $jsonHelper
     * @param \Plumrocket\Ajaxcart\Helper\Data                 $dataHelper
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\Ajaxcart\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Create json-encoded config for js
     *
     * @param array $config
     * @return string
     */
    public function getConfig(array $config = [])
    {
        $config['url']['toCart']        =   $this->getUrl('prajaxcart/cart/add');
        $config['url']['fromWishList']  =   $this->getUrl('prajaxcart/cart/addFromWishlist');
        $config['formSelectors']        =   $this->dataHelper->isAutomaticMode() ?
            '#product_addtocart_form  .box-tocart, form[data-role="tocart-form"], .action.tocart'
            : $this->dataHelper->getAtcBtnSelector();
        $config['popupSelector']        =   '#pac-popup-content';
        $config['asideSelector']        =   'aside.pac-modal-popup';
        $config['modalClass']           =   'pac-modal-popup';
        $config['workMode']             =   $this->dataHelper->getWorkMode();

        $config['buttonInnerHtml']      =   '<span class="pac-label">
                                                <span class="pac-helper"></span>
                                                <span class="pac-number"></span>
                                                <span class="pac-icon pac-sprite"></span>
                                                <span class="pac-loader"></span>
                                            </span>
                                            <span>' . __('Add To Cart') . '</span>';

        $config['notificationTemplate'] =   '<div class="pac-notification pac-error">
                                                <div>
                                                    <div class="pac-message">
                                                        {text}
                                                    </div>
                                                </div>
                                            </div>';
        $config['isCategory']           =   $this->getRequest()->getModuleName() == 'catalog'
        && $this->getRequest()->getControllerName() == 'category'
            ? 1
            : 0;
        $config['categoryId']           =   $this->registry->registry('current_category')
            ? (int)$this->registry->registry('current_category')->getId()
            : 0;

        return $this->jsonHelper->jsonEncode($config);
    }
}
