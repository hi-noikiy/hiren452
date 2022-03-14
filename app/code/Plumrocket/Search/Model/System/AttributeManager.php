<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Search Autocomplete & Suggest
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Search\Model\System;

class AttributeManager extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * AttributeManager constructor.
     *
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder             $criteriaBuilder
     * @param \Magento\Framework\Model\Context                         $context
     * @param \Magento\Framework\Registry                              $registry
     * @param array                                                    $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->criteriaBuilder = $criteriaBuilder;

        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @param $searhDataPriority
     */
    public function setAttributeSearchable($searhDataPriority)
    {
        $dataPriority = json_decode($searhDataPriority, true);
        $this->disableAttribute($dataPriority);

        $searchCriteria = $this->setFilterConditions('attribute_id', array_values($dataPriority), 'in');
        $dataPriority = array_flip($dataPriority);

        foreach ($this->getAttributeRepository($searchCriteria) as $attribute) {
            $prioriry = $dataPriority[$attribute->getAttributeId()];
            $attribute->setPsearchPriority($prioriry)->setIsSearchable(1)->save();
        }
    }

    /**
     * @param $dataPriority
     * @return bool
     */
    public function disableAttribute($dataPriority)
    {
        $searchCriteria = $this->setFilterConditions('psearch_priority', [0], 'neq');
        $result = [];

        foreach ($this->getAttributeRepository($searchCriteria) as $attribute) {
            $result[] = $attribute->getAttributeId();
        }

        $res = array_diff($result, $dataPriority);
        $searchCriteria = $this->setFilterConditions('attribute_id', array_values($res), 'in');

        foreach ($this->getAttributeRepository($searchCriteria) as $attribute) {
            $attribute->setPsearchPriority(0)->setIsSearchable(0)->save();
        }

        return true;
    }

    /**
     * @param $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface[]
     */
    private function getAttributeRepository($searchCriteria)
    {
        return $this->attributeRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param $field
     * @param $data
     * @param $type
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function setFilterConditions($field, $data, $type)
    {
        return $this->criteriaBuilder
            ->addFilter($field, $data, $type)
            ->create();
    }
}