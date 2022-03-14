<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Input;
use Magento\Framework\DataObject;
use Magento\Framework\Locale\CurrencyInterface;

class Amount extends Input
{
    protected $currnecyHelper;

    public function __construct(CurrencyInterface $currnecyHelper)
    {
        $this->currnecyHelper = $currnecyHelper;
    }

    public function render(DataObject $row)
    {
        $amount = $row->getData($this->getColumn()->getIndex());
        $currencycode = $row->getData('currency_code');
        if (!$amount || $currencycode == '') {
            return __('--');
        } else {
            return $this->currnecyHelper->getCurrency($currencycode)->getSymbol() . number_format($amount, 2);
        }
    }
}
