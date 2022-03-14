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

class Bestseller extends \Plumrocket\AmpEmailApi\Controller\AbstractStoreViewAction implements
    \Plumrocket\AmpEmail\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    /**
     * @var \Plumrocket\AmpEmail\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor
     */
    private $carouselDataExtractor;

    /**
     * Bestseller constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                  $context
     * @param \Magento\Store\Model\App\Emulation                                     $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface                             $storeManager
     * @param \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory                       $ampJsonFactory
     * @param \Plumrocket\AmpEmail\Api\CorsValidatorInterface                        $corsValidator
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface                   $tokenRepository
     * @param \Plumrocket\AmpEmail\Helper\Data                                       $dataHelper
     * @param \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor $carouselDataExtractor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory $ampJsonFactory,
        \Plumrocket\AmpEmail\Api\CorsValidatorInterface $corsValidator,
        \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository,
        \Plumrocket\AmpEmail\Helper\Data $dataHelper,
        \Plumrocket\AmpEmail\ViewModel\Component\Product\CarouselDataExtractor $carouselDataExtractor
    ) {
        parent::__construct($context, $appEmulation, $storeManager, $ampJsonFactory, $corsValidator, $tokenRepository);
        $this->dataHelper = $dataHelper;
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
        $period = (string) $this->getRequest()->getParam('period', 'month');

        $productsInfo = [];
        if ($this->dataHelper->isEnabledModuleBestsellers()) {
            /** @var \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface $bestsellersProvider */
            $bestsellersProvider = $this->_objectManager
                ->get('\Plumrocket\Bestsellers\Api\ProductIdsProviderInterface'); //@codingStandardsIgnoreLine

            $productIds = $bestsellersProvider->getByPeriod($period, $productCount ?: 10);
            if ($productIds) {
                $productsInfo = $this->carouselDataExtractor->execute($productIds, $customerId);
            }
        }

        $this->stopEmulation();

        return $ampJsonResult->setData(['products' => $productsInfo]);
    }
}
