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



namespace Mirasvit\ProductKit\Ui\Suggester\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Repository\KitRepository;

class DataProvider extends AbstractDataProvider
{
    private $kitRepository;

    private $kitItemRepository;

    /**
     * DataProvider constructor. DO NOT change "mixed"
     * @param KitRepository     $kitRepository
     * @param KitItemRepository $kitItemRepository
     * @param mixed             $name
     * @param mixed             $primaryFieldName
     * @param mixed             $requestFieldName
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        KitRepository $kitRepository,
        KitItemRepository $kitItemRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->kitRepository     = $kitRepository;
        $this->kitItemRepository = $kitItemRepository;

        $this->collection = $this->kitRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $model) {
            $data = $model->getData();

            $result[$model->getId()] = $data;
        }

        return $result;
    }
}
