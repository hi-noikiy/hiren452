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

namespace Mirasvit\ProductAction\Action\ChangeAttributeSet;

use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Action\Source\AttributeSetSource;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_ATTRIBUTE_SET_ID = 'attribute_set_id';

    use Element\SelectTrait;
    use Element\EnqueueTrait;

    private $attributeSetSource;

    public function __construct(
        AttributeSetSource $attributeSetSource
    ) {
        $this->attributeSetSource = $attributeSetSource;
    }

    public function getMeta(): array
    {
        return [
            $this->getSelect(
                self::PARAM_ATTRIBUTE_SET_ID,
                'Attribute Set',
                $this->attributeSetSource->toOptionArray()
            ),

            $this->getEnqueue(),
        ];
    }
}
