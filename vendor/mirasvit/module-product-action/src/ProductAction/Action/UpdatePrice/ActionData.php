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

namespace Mirasvit\ProductAction\Action\UpdatePrice;

use Mirasvit\ProductAction\Action\GenericActionData;

class ActionData extends GenericActionData
{
    public function getPrice(): string
    {
        return (string)$this->getData(MetaProvider::PARAM_PRICE_VALUE);
    }

    public function getCost(): string
    {
        return (string)$this->getData(MetaProvider::PARAM_COST_VALUE);
    }

    public function getSpecialPrice(): string
    {
        return (string)$this->getData(MetaProvider::PARAM_SPECIAL_PRICE_VALUE);
    }

    public function getSpecialPriceFromDate(): string
    {
        return (string)$this->getData(MetaProvider::PARAM_SPECIAL_PRICE_FROM);
    }

    public function getSpecialPriceToDate(): string
    {
        return (string)$this->getData(MetaProvider::PARAM_SPECIAL_PRICE_TO);
    }
}
