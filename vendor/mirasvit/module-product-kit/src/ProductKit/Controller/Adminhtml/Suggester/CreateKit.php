<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Controller\Adminhtml\Suggester;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductRepository;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductKit\Controller\Adminhtml\Kit\PostDataProcessor;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\SuggestionKitService;

class CreateKit extends Action
{
    private $kitRepository;

    private $postDataProcessor;

    private $productRepository;

    private $suggestionKitService;

    public function __construct(
        KitRepository $kitRepository,
        PostDataProcessor $postDataProcessor,
        ProductRepository $productRepository,
        SuggestionKitService $suggestionKitService,
        Action\Context $context
    ) {
        $this->kitRepository        = $kitRepository;
        $this->productRepository    = $productRepository;
        $this->postDataProcessor    = $postDataProcessor;
        $this->suggestionKitService = $suggestionKitService;

        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $error = '';
        try {
            $index = 1;
            $suggestion = $data['suggestion'];
            $products = [];
            $position = 1;
            foreach ($suggestion as $sku) {
                $product    = $this->productRepository->get($sku);
                $products[] = [
                    'id'              => $product->getId(),
                    'sku'             => $product->getSku(),
                    'position'        => $position,
                    'is_primary'      => 1,
                    'is_optional'     => 0,
                    'qty'             => 1,
                    'discount_type'   => ConfigProvider::DISCOUNT_TYPE_FIXED,
                    'discount_amount' => 0,
                ];

                $position++;
            }

            $kitData = [
                'kit_id'                => '',
                'is_smart'              => 0,
                'smart_blocks_amount'   => 0,
                'name'                  => __('Suggestion #%1', $index)->render(),
                'priority'              => 1,
                'block_title'           => __('Buy together')->render(),
                'is_active'             => 0,
                'general_discount_type' => ConfigProvider::DISCOUNT_TYPE_FIXED,
                'store_ids'             => [0],
                'customer_group_ids'    => [0],
                'links'                 => [
                    'product_kit_kit_form_product_listing' => $products,
                ],
            ];

            $model = $this->kitRepository->create();

            $this->postDataProcessor->setData($model, $kitData);

            $this->kitRepository->save($model);

            $kitItems = $this->postDataProcessor->getKitItems($model, $kitData);
            $this->kitRepository->saveItems($model, $kitItems);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'success' => empty($error),
            'error'   => $error,
            'kit_url' => $this->getUrl('product_kit/kit/edit', ['kit_id' => $model->getId()]),
        ]));

        return $response;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
