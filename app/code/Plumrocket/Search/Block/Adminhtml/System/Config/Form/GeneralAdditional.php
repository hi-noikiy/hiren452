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

namespace Plumrocket\Search\Block\Adminhtml\System\Config\Form;

class GeneralAdditional extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var \Plumrocket\Search\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $helperBackend;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * GeneralAdditional constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Config\Model\Config\Structure    $structure
     * @param \Plumrocket\Search\Helper\Data            $helper
     * @param \Magento\Backend\Helper\Data              $helperBackend
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Structure $structure,
        \Plumrocket\Search\Helper\Data $helper,
        \Magento\Backend\Helper\Data $helperBackend,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->structure = $structure;
        $this->helper = $helper;
        $this->helperBackend = $helperBackend;
        $this->objectManager = $objectManager;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Plumrocket_Search::system/config/general_additional.phtml');

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;

        return $this->toHtml();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        $options['Configuration -> Catalog -> Catalog Search'] = [
            $this->_getOption('catalog/search/min_query_length'),
            $this->_getOption('catalog/search/max_query_length'),
            $this->_getOption('catalog/search/engine'),
        ];

        $options['Configuration -> Inventory -> Stock Options'] = [
            $this->_getOption('cataloginventory/options/show_out_of_stock'),
        ];

        return $options;
    }

    /**
     * @param $path
     * @return array|bool|\Magento\Config\Model\Config\Structure\ElementInterface|null
     */
    protected function _getOption($path)
    {
        if (! $field = $this->structure->getElement($path)) {
            return false;
        }

        $field = array_merge(
            $field->getData(),
            [
                'name' => $field->getId(),
                'fullPath' => $path,
                'value' => $this->helper->getConfig($path),
                'url'   => $this->helperBackend->getUrl("adminhtml/system_config/edit/section/"
                    . $field->getPath() . '/' . $field->getId()),
            ]
        );

        switch ($field['type']) {
            case 'select':
                $field['valueText'] = '';
                if ($options = $this->objectManager->get($field['source_model'])->toOptionArray()) {
                    foreach ($options as $option) {
                        if ($option['value'] == $field['value']) {
                            $field['valueText'] = $option['label'];
                            break;
                        }
                    }
                }
                break;
            default:
                $field['valueText'] = (string)$field['value'];
        }

        return $field;
    }
}
