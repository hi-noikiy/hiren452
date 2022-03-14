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

namespace Mirasvit\ProductAction\Ui\Action\Source;

use Magento\Catalog\Model\Product\AttributeSet\Options as AttributeSetOptions;
use Magento\Framework\Data\OptionSourceInterface;

class AttributeSetSource implements OptionSourceInterface
{
    private $attributeSetOptions;

    public function __construct(AttributeSetOptions $attributeSetOptions)
    {
        $this->attributeSetOptions = $attributeSetOptions;
    }

    public function toOptionArray()
    {
        return $this->attributeSetOptions->toOptionArray();
    }
}

