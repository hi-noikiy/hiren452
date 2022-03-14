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

/**
 * Class Stock
 * @since 1.0.1
 */
class Stock extends \Plumrocket\AmpEmailApi\Controller\AbstractStoreViewAction implements
    \Plumrocket\AmpEmail\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    /**
     * @var \Plumrocket\AmpEmail\ViewModel\Component\Product\ExtractActualData
     */
    private $extractActualData;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * Stock constructor.
     *
     * @param \Magento\Framework\App\Action\Context                              $context
     * @param \Magento\Store\Model\App\Emulation                                 $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManager
     * @param \Plumrocket\AmpEmail\Model\Result\AmpJsonFactory                   $ampJsonFactory
     * @param \Plumrocket\AmpEmail\Api\CorsValidatorInterface                    $corsValidator
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface               $tokenRepository
     * @param \Plumrocket\AmpEmail\ViewModel\Component\Product\ExtractActualData $extractActualData
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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context, $appEmulation, $storeManager, $ampJsonFactory, $corsValidator, $tokenRepository);
        $this->extractActualData = $extractActualData;
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
            $customerId = $this->getTokenModel()->getCustomerId();

            $actualData = $this->extractActualData->execute($productId, $customerId);
            $actualData['updatedDateString'] = __('Information current as of %1', $this->getCurrentDate());

            $ampJsonResult
                ->setIsSingleListItem(true)
                ->setData($actualData);
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
