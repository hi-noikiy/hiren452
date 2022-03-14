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

namespace Plumrocket\AmpEmail\Controller\V1\ProductAlert\Actual;

class Difference extends \Plumrocket\AmpEmailApi\Controller\AbstractStoreViewAction implements
    \Plumrocket\AmpEmail\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    /**
     * @var \Plumrocket\AmpEmail\ViewModel\Component\Product\ExtractActualData
     */
    private $extractActualData;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * Difference constructor.
     *
     * @param \Magento\Framework\App\Action\Context                              $context
     * @param \Magento\Store\Model\App\Emulation                                 $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManager
     * @param \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory                   $ampJsonFactory
     * @param \Plumrocket\AmpEmail\Api\CorsValidatorInterface                    $corsValidator
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface               $tokenRepository
     * @param \Plumrocket\AmpEmail\ViewModel\Component\Product\ExtractActualData $extractActualData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface                  $priceCurrency
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface               $localeDate
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory $ampJsonFactory,
        \Plumrocket\AmpEmail\Api\CorsValidatorInterface $corsValidator,
        \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository,
        \Plumrocket\AmpEmail\ViewModel\Component\Product\ExtractActualData $extractActualData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context, $appEmulation, $storeManager, $ampJsonFactory, $corsValidator, $tokenRepository);
        $this->extractActualData = $extractActualData;
        $this->priceCurrency = $priceCurrency;
        $this->localeDate = $localeDate;
    }

    /**
     * @return \Plumrocket\AmpEmail\Model\Result\AmpJson
     */
    public function execute()
    {
        $ampJsonResult = $this->ampJsonFactory->create();

        $this->startEmulationForAmp();

        try {
            $productId = (int) $this->getRequest()->getParam('product');
            $initialPrice = $this->getRequest()->getParam('initialPrice');
            $customerId = $this->getTokenModel()->getCustomerId();

            $actualData = $this->extractActualData->execute($productId, $customerId);

            $difference = $initialPrice - $actualData['price'];

            $actualData['formattedDifference'] = $this->priceCurrency->format($difference);
            $actualData['updatedDateString'] = __('Information current as of %1', $this->getCurrentDate());

            if ($difference > 0) {
                $actualData['priceChange'] = 'down';
                $actualData['priceAlertTitle'] = __(
                    'The price for <b>%1</b> has dropped and is now %2 below your desired price.',
                    $actualData['name'],
                    $actualData['formattedDifference']
                );
            } else {
                $actualData['priceChange'] = 'up';
                $actualData['priceAlertTitle'] = __(
                    'The price for <b>%1</b> is no longer below your desired price. Please check back later for price updates.',
                    $actualData['name']
                );
            }

            $ampJsonResult->setData($actualData);
            $ampJsonResult->setIsSingleListItem(true);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $ampJsonResult->addExceptionMessage($e);
        }

        $this->stopEmulation();

        return $ampJsonResult;
    }

    /**
     * @return string
     */
    private function getCurrentDate() : string
    {
        return $this->localeDate->formatDateTime(
            null,
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL
        );
    }
}
