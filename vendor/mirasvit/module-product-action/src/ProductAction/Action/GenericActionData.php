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

namespace Mirasvit\ProductAction\Action;

use Magento\Framework\DataObject;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\MetaProviderInterface;

class GenericActionData extends DataObject implements ActionDataInterface
{
    /**
     * @param array $ids
     *
     * @return ActionDataInterface
     */
    public function setIds(array $ids): ActionDataInterface
    {
        return $this->setData(MetaProviderInterface::PARAM_IDS, $ids);
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return (array)$this->getData(MetaProviderInterface::PARAM_IDS);
    }

    /**
     * @param string $data
     *
     * @return ActionDataInterface
     */
    public function setActionData(string $data): ActionDataInterface
    {
        return $this->setData(MetaProviderInterface::PARAM_ACTION_DATA, $data);
    }

    /**
     * @return string
     */
    public function getActionData(): string
    {
        return (string)$this->getData(MetaProviderInterface::PARAM_ACTION_DATA);
    }
}
