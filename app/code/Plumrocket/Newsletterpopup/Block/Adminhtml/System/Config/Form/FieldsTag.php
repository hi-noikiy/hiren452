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

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form;

use Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\FieldsTag\InputTable;

/**
 * Class FieldsTag
 */
class FieldsTag extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /** @var InputTable $inputTableBlock */
        $inputTableBlock = $this->getLayout()->createBlock(InputTable::class);

        $inputTableBlock->setContainerFieldId($element->getName())
            ->setRowKey('name')
            ->addColumn(
                'orig_label',
                [
                    'header'    => __('Newsletter Popup Field'),
                    'index'     => 'orig_label',
                    'type'      => 'label',
                    'width'     => '36%',
                    'class'     => 'test',
                ]
            )->addColumn(
                'label',
                [
                    'header'    => __('Integration Field'),
                    'index'     => 'label',
                    'type'      => 'input',
                    'width'     => '28%',
                ]
            )->setArray($this->getPreparedValue($element->getValue()));

        return $inputTableBlock->toHtml();
    }

    /**
     * @param null $data
     * @return array
     */
    public function getPreparedValue($data = null)
    {
        $rows = [];

        if (is_array($data)) {
            return $data;
        }

        if (! empty($data)) {
            $data = json_decode($data, true);
        }

        if (is_array($data)) {
            foreach ($data as $origLabel => $label) {
                $rows[$origLabel] = [
                    'name' => $origLabel,
                    'orig_label' => $origLabel,
                    'label' => $label,
                ];
            }
        }

        return $rows;
    }
}
