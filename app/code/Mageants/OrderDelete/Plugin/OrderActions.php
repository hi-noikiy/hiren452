<?php
/**
 * @category  Magento OrderDelete
 * @package   Mageants_OrderDelete
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */

namespace Mageants\OrderDelete\Plugin;

class OrderActions
{
    protected $context;
    protected $urlBuilder;
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder
    )
    {
        $this->context = $context;
        $this->urlBuilder = $urlBuilder;
    }
    public function afterPrepareDataSource(
        \Magento\Sales\Ui\Component\Listing\Column\ViewAction $subject,
        array $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
           foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
                    $item[$this->getData('name')] = [
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    $urlEntityParamName => $item['entity_id']
                                ]
                            ),
                            'label' => __('Delete')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
