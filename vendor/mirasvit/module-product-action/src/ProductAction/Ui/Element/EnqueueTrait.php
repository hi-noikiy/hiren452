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

namespace Mirasvit\ProductAction\Ui\Element;

use Mirasvit\ProductAction\Api\MetaProviderInterface;

trait EnqueueTrait
{
    use ToggleTrait;

    public function getEnqueue(): array
    {
        $data = $this->elementToggle(
            MetaProviderInterface::PARAM_IS_ENQUEUE,
            __('Execute by cron')->getText()
        );

        $data['arguments']['data']['config']['additionalClasses'] = 'mst-product-action__element-toggle mst-product-action__element-enqueue';

        return $data;
    }
}
