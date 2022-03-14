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

namespace Mirasvit\ProductAction\Api;

interface MetaProviderInterface
{
    const PARAM_IDS         = 'mst_ids';
    const PARAM_CODE        = 'mst_code';
    const PARAM_CLASS       = 'mst_class';
    const PARAM_IS_ENQUEUE  = 'mst_is_enqueue';
    const PARAM_ACTION_DATA = 'mst_action_data';

    public function getMeta(): array;
}
