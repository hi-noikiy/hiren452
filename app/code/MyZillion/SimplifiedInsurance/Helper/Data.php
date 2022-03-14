<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package myzillion
 * @subpackage module-simplified-insurance
 * @author Serfe <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Data Constants
 */
class Data extends AbstractHelper
{
    const CUSTOMER_REQUEST_INSURANCE = 'customer_request_insurance';
    const OFFER_RESPONSE = 'offer_response';
    const ORDER_POST_RESPONSE = 'order_post_response';
    const IS_INSURABLE = 'is_insurable';

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Context                                         $context
     * @param \Magento\Framework\Serialize\SerializerInterface    $serializer
     * @param \Magento\Framework\Module\Dir\Reader                 $moduleReader
     * @param \Magento\Framework\Filesystem\Driver\File       $fileDriver
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->serializer = $serializer;
        $this->moduleReader = $moduleReader;
        $this->fileDriver = $fileDriver;
        $this->logger = $logger;
    }

    /**
     * Check if there is a response for the shipment within the response of the order
     *
     * @param array $orderResponse
     * @param integer $shipmentId
     * @return boolean
     */
    public function checkShipmentHaveResponseInOrder($orderResponse, $shipmentId)
    {
        if (array_search($shipmentId, array_column($orderResponse, 'shipment_id')) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the current module version from the composer.json file
     *
     * @return string|null
     */
    public function getModuleVersionFromComposer()
    {
        $version = null;
        try {
            $path = $this->getComposerJsonFilePath();
            $content = $this->fileDriver->fileGetContents($path);
            if (!empty(trim($content))) {
                $content = (json_decode($content, true));
                if (isset($content['version'])) {
                    $version = $content['version'];
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getTraceAsString());
        }

        return $version;
    }

    /**
     * Retrieve the composer.json file path
     *
     * @return string
     */
    protected function getComposerJsonFilePath()
    {
        $baseDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'MyZillion_SimplifiedInsurance'
        );
        $path = $this->fileDriver->getParentDirectory($baseDir) . '/composer.json';
        return $path;
    }
}
