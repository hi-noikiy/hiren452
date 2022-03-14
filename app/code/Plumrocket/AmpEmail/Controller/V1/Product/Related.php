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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Controller\V1\Product;

class Related extends \Plumrocket\AmpEmailApi\Controller\AbstractStoreViewAction implements
    \Plumrocket\AmpEmail\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    /**
     * @var \Plumrocket\AmpEmail\ViewModel\Component\Product\GetRelatedProducts
     */
    private $getRelatedProducts;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor
     */
    private $carouselDataExtractor;

    /**
     * Related constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                  $context
     * @param \Magento\Store\Model\App\Emulation                                     $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface                             $storeManager
     * @param \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory                       $ampJsonFactory
     * @param \Plumrocket\AmpEmail\Api\CorsValidatorInterface                        $corsValidator
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface                   $tokenRepository
     * @param \Plumrocket\AmpEmail\ViewModel\Component\Product\GetRelatedProducts    $getRelatedProducts
     * @param \Magento\Sales\Api\OrderRepositoryInterface                            $orderRepository
     * @param \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor $carouselDataExtractor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory $ampJsonFactory,
        \Plumrocket\AmpEmail\Api\CorsValidatorInterface $corsValidator,
        \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository,
        \Plumrocket\AmpEmail\ViewModel\Component\Product\GetRelatedProducts $getRelatedProducts,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor $carouselDataExtractor
    ) {
        parent::__construct($context, $appEmulation, $storeManager, $ampJsonFactory, $corsValidator, $tokenRepository);
        $this->getRelatedProducts = $getRelatedProducts;
        $this->orderRepository = $orderRepository;
        $this->carouselDataExtractor = $carouselDataExtractor;
    }

    /**
     * @return \Plumrocket\AmpEmail\Model\Result\AmpJson
     */
    public function execute()
    {
        $ampJsonResult = $this->ampJsonFactory->create();

        $this->startEmulationForAmp();

        $customerId = $this->getTokenModel()->getCustomerId();
        $productCount = (int) $this->getRequest()->getParam('count');
        $storeId = (int) $this->getRequest()->getParam('store');
        $orderId = (int) $this->getRequest()->getParam('order_id');

        try {
            /** @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order */
            $order = $this->orderRepository->get($orderId);

            $orderItems = $order->getAllVisibleItems();

            $orderProductIds = [];
            foreach ($orderItems as $item) {
                $orderProductIds[] = (int) $item['product_id'];
            }

            $relatedProductIds = $this->getRelatedProducts->execute($orderProductIds, $storeId, $productCount ?: 10);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $relatedProductIds = [];
        }

        $productsInfo = [];
        if ($relatedProductIds) {
            $productsInfo = $this->carouselDataExtractor->execute($relatedProductIds, $customerId);
        }

        $this->stopEmulation();

        return $ampJsonResult->setData(['products' => $productsInfo]);
    }
}
