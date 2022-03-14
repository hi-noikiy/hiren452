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

use Mirasvit\ProductAction\Action\GenericActionData;

class ActionData extends GenericActionData
{
    public function setIsRemove(bool $flag): ActionData
    {
        return $this->setData(MetaProvider::PARAM_IS_REMOVE, $flag);
    }

    public function getIsRemove(): bool
    {
        return (bool)$this->getData(MetaProvider::PARAM_IS_REMOVE);
    }

    public function setIsCopy(bool $flag): ActionData
    {
        return $this->setData(MetaProvider::PARAM_IS_COPY, $flag);
    }

    public function getIsCopy(): bool
    {
        return (bool)$this->getData(MetaProvider::PARAM_IS_COPY);
    }

    public function setCopyFrom(array $ids): ActionData
    {
        return $this->setData(MetaProvider::PARAM_PARENT_SKU, $ids);
    }

    public function getCopyFrom(): array
    {
        $data = $this->getData(MetaProvider::PARAM_PARENT_SKU);

        if ($data) {
            return (array)explode(',', $this->getData(MetaProvider::PARAM_PARENT_SKU));
        } else {
            return [];
        }
    }
}
