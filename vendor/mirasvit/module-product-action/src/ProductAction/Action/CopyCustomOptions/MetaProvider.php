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

namespace Mirasvit\ProductAction\Action\CopyCustomOptions;

use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Ui\Element;

class MetaProvider implements MetaProviderInterface
{
    const PARAM_COPY_FROM          = 'copy_from';
    const PARAM_IS_REPLACE_OPTIONS = 'is_replace_options';

    use Element\ProductSelectorTrait;
    use Element\EnqueueTrait;

    public function getMeta(): array
    {
        return [
            $this->elementProductSelector(self::PARAM_COPY_FROM, 'Copy from'),
            $this->getEnqueue(),

            self::PARAM_IS_REPLACE_OPTIONS => $this->elementToggle(self::PARAM_IS_REPLACE_OPTIONS, __('Add/Update mode')->getText(), [
                'default' => 0,
            ]),
        ];
    }
}
