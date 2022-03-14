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
 * @package   mirasvit/module-event
 * @version   1.2.41
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\EventData\Condition;

use Mirasvit\Event\EventData\AdminData;

class AdminCondition extends AbstractCondition
{
    /**
     * @return string
     */
    public function getEventDataClass()
    {
        return AdminData::class;
    }
}
