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
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Grid\Column;

class Network extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * Type factory
     * @var \Plumrocket\Affiliate\Model\Type
     */
    protected $_typeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context     
     * @param \Plumrocket\Affiliate\Model\TypeFactory $typeFactory 
     * @param array                                   $data        
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Affiliate\Model\TypeFactory $typeFactory,
        array $data = []
    ) {
        $this->_typeFactory = $typeFactory;
        parent::__construct($context, $data);
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateNetwork'];
    }


    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateNetwork($value, $row, $column, $isExport)
    {
        $type = $this->_typeFactory->create()->load($row->getTypeId());

        if ($type->getKey() === 'tradedoubler') {
            $cell = '<div align="center"><img src="' . $this->getViewFileUrl('Plumrocket_Affiliate::images/' . strtolower($type->getKey()) . 'logo.png') . '" /></div>';
        } elseif ($type->getKey() !== 'custom') {
            $cell = '<div align="center"><img src="' . $this->getViewFileUrl('Plumrocket_Affiliate::images/' . strtolower($type->getKey()) . '.png') . '" /></div>';
        } else {
            $cell = '<div align="center"><strong>CUSTOM</strong></div>';
        }
        return $cell;
    }
}
