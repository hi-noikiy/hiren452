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

namespace Plumrocket\AmpEmail\Model\Image;

class Compressor implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Media\ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Plumrocket\AmpEmail\Api\ResizeImageInterface
     */
    private $resizeImage;

    /**
     * Compressor constructor.
     *
     * @param \Magento\Catalog\Model\Product\Media\ConfigInterface $config
     * @param \Magento\Catalog\Helper\Image                        $imageHelper
     * @param \Plumrocket\AmpEmail\Api\ResizeImageInterface        $resizeImage
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Media\ConfigInterface $config,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Plumrocket\AmpEmail\Api\ResizeImageInterface $resizeImage
    ) {
        $this->config = $config;
        $this->imageHelper = $imageHelper;
        $this->resizeImage = $resizeImage;
    }

    /**
     * @param      $image
     * @param int  $width
     * @param null $height
     * @return array|bool
     */
    public function getProductGalleryPreviewImage($image, $width = 120, $height = null)
    {
        if (! $height) {
            $height = 0;
        }

        $resizedImage = $this->resizeImage->execute(
            $image,
            $width,
            $height,
            $this->config->getBaseMediaPath(),
            'pramp',
            false
        );

        if (! $resizedImage) {
            $resizedImage = $this->resizeImage->execute(
                $this->imageHelper->getDefaultPlaceholderUrl(),
                $width,
                $height,
                $this->config->getBaseMediaPath(),
                'pramp',
                false
            );
        }

        return $resizedImage;
    }
}
