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
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Service\Suggester;


class Timer
{
    /**
     * @var float
     */
    private $finish;

    /**
     * @param int $duration
     * @return void
     */
    public function start($duration)
    {
        $this->finish = microtime(true) + $duration;
    }

    public function isTimeout()
    {
        return microtime(true) > $this->finish;
    }

}
