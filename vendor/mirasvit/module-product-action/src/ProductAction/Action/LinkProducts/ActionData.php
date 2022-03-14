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

use Mirasvit\ProductAction\Action\GenericActionData;
use Mirasvit\ProductAction\Api\LinkActionDataInterface;
use Mirasvit\ProductAction\Api\MetaProviderInterface;

class ActionData extends GenericActionData implements LinkActionDataInterface
{
    public function setLinkType(int $type): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_LINK_TYPE, $type);
    }

    public function getLinkType(): int
    {
        return (int)$this->getData(MetaProvider::PARAM_LINK_TYPE);
    }

    public function setCode(string $code): LinkActionDataInterface
    {
        return $this->setData(MetaProviderInterface::PARAM_CODE, $code);
    }

    public function getCode(): string
    {
        return (string)$this->getData(MetaProviderInterface::PARAM_CODE);
    }

    public function setDirection(int $type): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_DIRECTION, $type);
    }

    public function getDirection(): int
    {
        return (int)$this->getData(MetaProvider::PARAM_DIRECTION);
    }

    public function setRemoveAll(bool $type): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_REMOVE_ALL, $type);
    }

    public function getRemoveAll(): bool
    {
        return (bool)$this->getData(MetaProvider::PARAM_REMOVE_ALL);
    }

    public function setAddProductIds(array $ids): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_ADD, $ids);
    }

    public function getAddProductIds(): array
    {
        $data = $this->getData(MetaProvider::PARAM_ADD);

        if ($data) {
            return (array)explode(',', $this->getData(MetaProvider::PARAM_ADD));
        } else {
            return [];
        }
    }

    public function setRemoveProductIds(array $ids): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_REMOVE, $ids);
    }

    public function getRemoveProductIds(): array
    {
        $data = $this->getData(MetaProvider::PARAM_REMOVE);

        if ($data) {
            return (array)explode(',', $this->getData(MetaProvider::PARAM_REMOVE));
        } else {
            return [];
        }
    }

    public function setCopyProductIds(array $ids): LinkActionDataInterface
    {
        return $this->setData(MetaProvider::PARAM_COPY, $ids);
    }

    public function getCopyProductIds(): array
    {
        $data = $this->getData(MetaProvider::PARAM_COPY);

        if ($data) {
            return (array)explode(',', $this->getData(MetaProvider::PARAM_COPY));
        } else {
            return [];
        }
    }
}
