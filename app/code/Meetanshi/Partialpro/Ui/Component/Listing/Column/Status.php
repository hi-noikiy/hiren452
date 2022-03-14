<?php

namespace Meetanshi\Partialpro\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Status extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {

                $state = $item[$fieldName];
                if ($state == 1) {
                    $colour = "86cae4";
                    $value = "Processing";
                } elseif ($state == 2) {
                    $colour = "f7944b";
                    $value = "Paid";
                } else {
                    $colour = "434a56";
                    $value = "Pending";
                }
                $item[$fieldName] = '<div style="text-align:center;width: 110px !important;    padding: 5px 0; color:#FFF;background-color:#' . $colour . ';border-radius:8px;width:100%">' . $value . '</div>';
            }
        }
        return $dataSource;
    }
}