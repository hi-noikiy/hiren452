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



namespace Mirasvit\ProductKit\Controller\Adminhtml\Kit;

use Magento\Backend\App\Action;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\KitBuilderService;

class Evaluator extends Action
{
    private $kitRepository;

    private $postDataProcessor;

    private $kitBuilderService;

    public function __construct(
        KitRepository $kitRepository,
        PostDataProcessor $postDataProcessor,
        KitBuilderService $kitBuilderService,
        Action\Context $context
    ) {
        $this->kitRepository     = $kitRepository;
        $this->postDataProcessor = $postDataProcessor;
        $this->kitBuilderService = $kitBuilderService;

        parent::__construct($context);
    }

    public function execute()
    {
        $model = $this->kitRepository->create();

        $data = $this->postDataProcessor->filterPostData(
            $this->getRequest()->getParams()
        );

        $this->postDataProcessor->setData($model, $data);

        $kitItems = $this->postDataProcessor->getKitItems($model, $data);
        foreach ($kitItems as $idx => $item) {
            $item->setData(KitItemInterface::ID, $idx);
        }

        $letters = $this->getLetters($kitItems);

        $combinations = $this->kitBuilderService->getItemCombinations($kitItems);

        $result = [];
        foreach ($combinations as $idx => $combination) {
            $offerItems = $this->kitBuilderService->getOfferItems($kitItems, $combination);

            foreach ($offerItems as $item) {
                $id = $item->getId();

                $amount = '-';

                if ($item->getDiscountAmount()) {
                    if ($item->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_FIXED) {
                        $amount = '$' . $item->getDiscountAmount();
                    }

                    if ($item->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_PERCENTAGE) {
                        $amount = $item->getDiscountAmount() . '%';
                    }
                }

                $result[$idx][] = [
                    'name'   => __('Product %1', $letters[$id])->__toString(),
                    'letter' => $letters[$id],
                    'amount' => $amount,
                ];
            }
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'success' => true,
            'items'   => $result,
        ]));

        return $response;
    }

    /**
     * @param KitItemInterface[] $kitItems
     *
     * @return array
     */
    private function getLetters($kitItems)
    {
        $letters = [];
        foreach ($kitItems as $kitItem) {
            $letter = chr(64 + $kitItem->getPosition());
            $id     = $kitItem->getId();

            $letters[$id] = $letter;
        }

        return $letters;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
