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
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductKit\Service\SuggestionKitService;

class Suggest extends Action
{
    private $suggestionKitService;

    public function __construct(
        SuggestionKitService $suggestionKitService,
        Action\Context $context
    ) {
        $this->suggestionKitService = $suggestionKitService;

        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $result = $this->suggestionKitService->getProductsByFilters($data);

        $message = count($result) ? '' : __('We can\'t find any suggestions. Try adjusting the selection criteria')->render();

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'success' => true,
            'items'   => $result,
            'message' => $message,
        ]));

        return $response;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
