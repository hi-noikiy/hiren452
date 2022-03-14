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
 * @package   mirasvit/module-report-api
 * @version   1.0.43
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Config\Type;

use Mirasvit\ReportApi\Api\Config\TypeInterface;

class Pk extends Number implements TypeInterface
{
    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_PK;
    }

    /**
     * @return array|string[]
     */
    public function getAggregators()
    {
        return ['none', 'cnt'];
    }
}