<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

use Magento\Backend\App\Action\Context;
use FME\Mediaappearance\Model\Mediaappearance as Mediaappearance;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var PostDataProcessor */
    protected $dataProcessor;

    /** @var Mediaappearance  */
    protected $mediaappearance;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param Mediaappearance $mediaappearance
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Mediaappearance $mediaappearance,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->mediaappearance = $mediaappearance;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
       // print_r($postItems);
       // exit;
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $mediaappearanceId) {
            /** @var \Magento\Mediaappearance\Model\Mediaappearance $mediaappearance */
            $mediaappearance = $this->mediaappearance->load($mediaappearanceId);
            try {
                $mediaappearanceData = $this->dataProcessor->filter($postItems[$mediaappearanceId]);
                $mediaappearance->setData($mediaappearanceData);
                $mediaappearance->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithMediaappearanceId($mediaappearance, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithMediaappearanceId($mediaappearance, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithMediaappearanceId(
                    $mediaappearance,
                    __('Something went wrong while saving the mediaappearance.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add mediaappearance title to error message
     *
     * @param MediaappearanceInterface $mediaappearance
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithMediaappearanceId(Mediaappearance $mediaappearance, $errorText)
    {
        return '[Mediaappearance ID: ' . $mediaappearance->getMediaappearanceId() . '] ' . $errorText;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
