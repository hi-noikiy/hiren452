<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\UpdateGallery;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Gallery\Processor;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

class Executor implements ExecutorInterface
{
    private $processor;

    private $productRepository;

    public function __construct(
        Processor $processor,
        ProductRepositoryInterface $productRepository
    ) {
        $this->processor         = $processor;
        $this->productRepository = $productRepository;
    }

    public function execute(ActionDataInterface $actionData): void
    {
        $actionData = $this->cast($actionData);

        if ($actionData->getIsCopy()) {
            $this->copyImages($actionData);
        }

        if ($actionData->getIsRemove()) {
            $this->removeImages($actionData);
        }
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }

    private function copyImages(ActionData $actionData)
    {
        $attributeCodes = $this->processor->getMediaAttributeCodes();
        foreach ($actionData->getIds() as $id) {
            /** @var \Magento\Catalog\Model\Product $p */
            $p = $this->productRepository->getById($id, true);

            foreach ($actionData->getCopyFrom() as $sku) {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->get($sku, true);

                foreach ($attributeCodes as $code) {
                    if ($p->getData($code) == 'no_selection') {
                        $p->setData($code, $product->getData($code));
                    }
                }

                $position = (int)$product->getMediaGalleryImages()->count();

                foreach ($product->getMediaGalleryImages() as $imageInfo) {

                    $mediaAttributes = [];
                    foreach ($attributeCodes as $code) {
                        if ($imageInfo['file'] == $p->getData($code)) {
                            $mediaAttributes[] = $code;
                        }
                    }

                    $imageFileUri = $p->addImageToMediaGallery(
                        $imageInfo['path'], $mediaAttributes ?: null, false, $imageInfo['disabled']);

                    $this->processor->updateImage(
                        $p,
                        $imageFileUri,
                        [
                            'label'      => $imageInfo['label'],
                            'position'   => $position,
                            'disabled'   => $imageInfo['disabled'],
                            'media_type' => $mediaAttributes,
                        ]
                    );

                    $position++;
                }

                $this->productRepository->save($p);
            }
        }
    }

    private function removeImages(ActionData $actionData)
    {
        $attributeCodes = $this->processor->getMediaAttributeCodes();

        foreach ($actionData->getIds() as $id) {
            /** @var \Magento\Catalog\Model\Product $p */
            $p = $this->productRepository->getById($id, true);

            foreach ($attributeCodes as $code) {
                $p->setData($code, null);
            }

            $this->productRepository->save($p);

            foreach ($p->getMediaGalleryImages() as $imageInfo) {
                $this->processor->removeImage($p, $imageInfo['file']);
            }

            $this->productRepository->save($p);
        }
    }
}
