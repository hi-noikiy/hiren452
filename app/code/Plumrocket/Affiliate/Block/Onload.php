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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Plumrocket\Affiliate\Model\Affiliate\CommissionJunction;
use Plumrocket\Affiliate\Model\Affiliate\Hasoffers;
use Plumrocket\Affiliate\Model\Affiliate\Pepperjam;
use Plumrocket\Affiliate\Model\Affiliate\Tradedoubler;

class Onload extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Plumrocket\Affiliate\Helper\Data                 $dataHelper
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        array $data = []
    ) {
        $this->cookieManager = $cookieManager;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return '';
        }

        $html = "
            <script>
            require(['jquery', 'jquery/jquery.cookie'], function ($) {
                'use strict';
            ";

        $done = [];

        foreach ($this->dataHelper->getPageAffiliates() as $affiliate) {
            if (in_array($affiliate->getTypeId(), $done)) {
                continue;
            }

            $done[] = $affiliate->getTypeId();
            $request = $this->getRequest();

            switch ($affiliate->getTypeId()) {
                case Tradedoubler::TYPE_ID:
                    if ($tduid = $request->getParam('tduid')) {
                        $html .= "$.cookie('" . (Tradedoubler::STORAGE_NAME)
                            . "', '{$this->escapeHtml($tduid)}');";
                    }
                    break;
                case Hasoffers::TYPE_ID:
                    $params = $request->getParams();
                    $additionalData = $affiliate->getAdditionalDataArray();
                    $html .= "
                        var hoData = JSON.parse($.cookie('" . (Hasoffers::STORAGE_NAME) . "'));
                        if (!hoData) hoData = {};";
                    $changed = false;

                    foreach ($additionalData['postback_params'] as $item) {
                        $key = $item['value'];

                        if (! empty($params[$key])) {
                            $html .= "hoData['{$key}'] = '{$params[$key]}';";
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        $html .= "$.cookie('" . (Hasoffers::STORAGE_NAME) . "', hoData);";
                    }
                    break;

                case CommissionJunction::TYPE_ID:
                    if ($cjevent = $request->getParam('cjevent')) {
                        $html .= "$.cookie('" . (CommissionJunction::STORAGE_NAME)
                            . "', '{$this->escapeHtml($cjevent)}', { expires: 60, path: '/' });";
                    }
                    break;

                case Pepperjam::TYPE_ID:
                    if ($clickId = $request->getParam('clickId')) {
                        $cookieData = $this->cookieManager->getCookie(Pepperjam::STORAGE_NAME);
                        $jsonSerializer = ObjectManager::getInstance()->get(Json::class);
                        $cookieData = ! $cookieData ? [] : $jsonSerializer->unserialize($cookieData);

                        if (! in_array($clickId, $cookieData)) {
                            $cookieData[] = $clickId;
                            $encodedData = $jsonSerializer->serialize($cookieData);
                            $html .= "$.cookie('" . (Pepperjam::STORAGE_NAME) . "', '{$encodedData}');";
                        }
                    }
                    break;
            }
        }

        $html .= "
            });
        </script>";

        return $html;
    }
}
