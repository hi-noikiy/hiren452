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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Helper;

class Cors extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * Cors constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Response\Http  $response
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Response\Http $response
    ) {
        parent::__construct($context);
        $this->response = $response;
    }

    /**
     * Processing HTTP-Headers for cross domain requests
     * Setting additional headers for same-origin and cross-origin requests
     * according to https://github.com/ampproject/amphtml/blob/master/spec/amp-cors-requests.md
     *
     * @param \Magento\Framework\Controller\AbstractResult|null $result
     * @return $this
     */
    public function prepareHeadersForAmpResponse(\Magento\Framework\Controller\AbstractResult $result = null) : self
    {
        return $this->removeSameOriginHeaders()->setAccessControlHeaders($result);
    }

    /**
     * @return $this
     */
    private function removeSameOriginHeaders() : self
    {
        $headers = $this->response->getHeaders();
        $this->response->clearHeaders();

        foreach ($headers as $header) {
            if (($header['name'] !== 'X-Frame-Options')
                && ($header['name'] !== 'Content-Security-Policy')
            ) {
                $this->response->setHeader($header['name'], $header['value'], $header['replace']);
            }
        }

        return $this;
    }

    /**
     * Set Access Control Headers
     *
     * @param \Magento\Framework\Controller\AbstractResult|null $result
     * @return $this
     */
    private function setAccessControlHeaders(\Magento\Framework\Controller\AbstractResult $result = null) : self
    {
        $sourceOrigin = $this->_request->getParam('__amp_source_origin', '');

        /** @var \Magento\Framework\Controller\AbstractResult|\Magento\Framework\App\Response\Http $objectForHeader */
        $objectForHeader = $result ?: $this->response;

        $objectForHeader
            ->setHeader(
                'Access-Control-Allow-Origin',
                $this->getAccessControlOrigin(),
                true
            )
            ->setHeader(
                'AMP-Access-Control-Allow-Source-Origin',
                $sourceOrigin,
                true
            )
            ->setHeader(
                'Access-Control-Expose-Headers',
                'AMP-Access-Control-Allow-Source-Origin',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Methods',
                'POST, GET, OPTIONS',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Headers',
                'Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Credentials',
                'true',
                true
            );

        return $this;
    }

    /**
     * Retrieve source origin from request
     *
     * @return string
     */
    private function getAccessControlOrigin() : string
    {
        if ($httpOrigin = $this->_getRequest()->getServer('HTTP_ORIGIN')) {
            return $httpOrigin;
        }

        return '';
    }
}
