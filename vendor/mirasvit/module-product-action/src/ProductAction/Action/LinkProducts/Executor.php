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

namespace Mirasvit\ProductAction\Action\LinkProducts;

use Magento\Catalog\Model\ResourceModel\Product\Link;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;
use Mirasvit\ProductAction\Api\LinkActionDataInterface;

class Executor implements ExecutorInterface
{
    private $connection;

    private $link;

    private $actionDataFactory;

    private $linkProductsService;

    private $metaModifier;

    private $resource;

    public function __construct(
        Service\LinkProductsService $linkProductsService,
        Link $link,
        MetaProvider $metaModifier,
        ResourceConnection $resource,
        ActionDataFactory $actionDataFactory
    ) {
        $this->link           = $link;
        $this->metaModifier   = $metaModifier;
        $this->resource       = $resource;
        $this->connection     = $resource->getConnection();

        $this->actionDataFactory = $actionDataFactory;
        $this->linkProductsService   = $linkProductsService;
    }

    public function modifyMeta(array $meta): array
    {
        return $this->metaModifier->getMeta();
    }

    /**
     * @param LinkActionDataInterface $actionData
     */
    public function execute(ActionDataInterface $actionData): void
    {
        $actionData = $this->cast($actionData);

        switch ($actionData->getCode()) {
            case LinkActionDataInterface::TYPE_RELATED_CODE:
                $actionData->setLinkType(LinkActionDataInterface::TYPE_RELATED_PRODUCTS);
                break;
            case LinkActionDataInterface::TYPE_UPSELL_CODE:
                $actionData->setLinkType(LinkActionDataInterface::TYPE_UPSELL_PRODUCTS);
                break;
            case LinkActionDataInterface::TYPE_CROSSSELL_CODE:
                $actionData->setLinkType(LinkActionDataInterface::TYPE_CROSSSELL_PRODUCTS);
                break;
        }

        $this->linkProductsService->link($actionData);
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }
}
