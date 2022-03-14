<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Config\Source\Integration;

/**
 * Class IntegrationList
 */
abstract class AbstractIntegrationList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return null|\Plumrocket\Newsletterpopup\Model\AbstractIntegration
     */
    abstract public function getModel();

    /**
     * @return array
     */
    public function toOptionHash()
    {
        $model = $this->getModel();

        return $model instanceof \Plumrocket\Newsletterpopup\Model\AbstractIntegration
            ? $model->getAllLists() : [];
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $values = $this->toOptionHash();
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = [
                'value'    => $key,
                'label'    => $value,
            ];
        }

        return $result;
    }
}
