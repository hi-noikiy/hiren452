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

namespace Mirasvit\ProductAction;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Api\Data\StoreInterface;
use Mirasvit\ProductAction\Api\ActionInterface;

class Registry
{
    private $coreRegistry;

    private $currentAction;

    public function __construct(
        CoreRegistry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    public function getCurrentAction(): ?ActionInterface
    {
        return $this->currentAction;
    }

    public function setCurrentAction(?ActionInterface $currentAction): void
    {
        $this->currentAction = $currentAction;
    }

    public function getCurrentStore(): ?StoreInterface
    {
        return $this->coreRegistry->registry('current_store');
    }

    public function setCurrentStore(?StoreInterface $currentStore): void
    {
        $this->coreRegistry->unregister('current_store');
        $this->coreRegistry->register('current_store', $currentStore);
    }
}
