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



namespace Mirasvit\ProductKit\Ui\Kit\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\ProductKit\Api\Data\KitInterface;

class Actions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit'    => [
                        'href'  => $this->context->getUrl('product_kit/kit/edit', [
                            KitInterface::ID => $item[KitInterface::ID],
                        ]),
                        'label' => __('Edit'),
                    ],
                    'reindex' => [
                        'href'  => $this->context->getUrl('product_kit/kit/reindex', [
                            KitInterface::ID => $item[KitInterface::ID],
                        ]),
                        'label' => __('Reindex'),
                    ],
                    'delete'  => [
                        'href'    => $this->context->getUrl('product_kit/kit/delete', [
                            KitInterface::ID => $item[KitInterface::ID],
                        ]),
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title'   => __('Delete "%1"', $item['name']),
                            'message' => __('Are you sure you want to delete a "%1" record?', $item['name']),
                        ],
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
