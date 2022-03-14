<?php

namespace Meetanshi\Partialpro\Ui\Component\Listing\Column;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Amount extends Column
{

    protected $currnecyHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CurrencyInterface $currnecyHelper,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->currnecyHelper = $currnecyHelper;
    }

    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $amount = $item[$fieldName];
                $currencycode = $item['currency_code'];
                if (!$amount || $currencycode == '') {
                    $item[$fieldName] = '--';
                } else {
                    $item[$fieldName] = $this->currnecyHelper->getCurrency($currencycode)->getSymbol() . number_format($amount, 2);
                }
            }
        }
        return $dataSource;
    }
}