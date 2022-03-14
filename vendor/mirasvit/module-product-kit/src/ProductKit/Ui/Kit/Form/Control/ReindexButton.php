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



namespace Mirasvit\ProductKit\Ui\Kit\Form\Control;

use Mirasvit\ProductKit\Api\Data\KitInterface;

class ReindexButton extends GenericButton
{
    public function getButtonData()
    {
        $data = [];

        if ($this->getId()) {
            $data = [
                'label'      => __('Reindex'),
                'on_click'   => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getReindexUrl() . '\')',
                'sort_order' => 25,
            ];
        }

        return $data;
    }

    public function getReindexUrl()
    {
        return $this->getUrl('*/*/reindex', [KitInterface::ID => $this->getId()]);
    }
}
