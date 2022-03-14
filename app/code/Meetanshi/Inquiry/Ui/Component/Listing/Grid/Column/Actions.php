<?php

namespace Meetanshi\Inquiry\Ui\Component\Listing\Grid\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Escaper;

class Actions extends Column
{
    const URL_PATH_DELETE = 'meetanshi_inquiry/inquiry/delete';
    const URL_PATH_EDIT = 'meetanshi_inquiry/inquiry/addrow';
    const URL_PATH_CREATE_CUSTOMER = 'meetanshi_inquiry/inquiry/createcustomer';
    protected $urlBuilder;
    protected $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['dealer_id'])) {
                    if (strpos($item['is_customer_created'], "Not") === false) {
                        $item[$this->getData('name')] = [
                            'Edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_EDIT,
                                    [
                                        'id' => $item['dealer_id']
                                    ]
                                ),
                                'label' => __('Edit Inquiry')
                            ],
                            'Delete' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_DELETE,
                                    [
                                        'id' => $item['dealer_id']
                                    ]
                                ),
                                'label' => __('Delete Dealer'),
                                'confirm' => [
                                    'title' => __('Delete'),
                                    'message' => __('Are you sure you wan\'t to delete a record?')
                                ]
                            ]
                        ];
                    } else {
                        $item[$this->getData('name')] = [
                            'Edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_EDIT,
                                    [
                                        'id' => $item['dealer_id']
                                    ]
                                ),
                                'label' => __('Edit Inquiry')
                            ],
                            'Create Customer' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_CREATE_CUSTOMER,
                                    [
                                        'id' => $item['dealer_id']
                                    ]
                                ),
                                'label' => __('Create Customer')
                            ],
                            'Delete' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_DELETE,
                                    [
                                        'id' => $item['dealer_id']
                                    ]
                                ),
                                'label' => __('Delete Dealer'),
                                'confirm' => [
                                    'title' => __('Delete'),
                                    'message' => __('Are you sure you wan\'t to delete a record?')
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
