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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Queue;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Notification\NotifierInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\MetaProviderInterface;
use Mirasvit\ProductAction\Action\GenericActionDataFactory;
use Mirasvit\ProductAction\Repository\ActionRepository;

class Consumer
{
    private $actionDataFactory;

    private $actionRepository;

    private $logger;

    private $notifier;

    public function __construct(
        ActionRepository $actionRepository,
        \Psr\Log\LoggerInterface $logger,
        NotifierInterface $notifier,
        GenericActionDataFactory $actionDataFactory
    ) {
        $this->actionDataFactory = $actionDataFactory;
        $this->actionRepository  = $actionRepository;

        $this->logger   = $logger;
        $this->notifier = $notifier;
    }

    public function process(ActionDataInterface $actionData): void
    {
        try {
            $params = SerializeService::decode($actionData->getActionData());
            $action = $this->actionRepository->get($params[MetaProviderInterface::PARAM_CODE]);

            $objectManager = ObjectManager::getInstance();
            $actionData    = $objectManager->create($params[MetaProviderInterface::PARAM_CLASS], ['data' => $params]);

            $action->getExecutor()->execute($actionData);

            $this->notifier->addMajor(
                (string)__('Action "%1" was processed', $action->getLabel()),
                (string)__('Action "%1" was processed', $action->getLabel())
            );
        } catch (LocalizedException | FileSystemException $exception) {
            $this->notifier->addCritical(
                (string)__('Error during process additional product action'),
                (string)__('Error during process additional product action')
            );
            $this->logger->critical((string)__('Something went wrong while process additional product action. %1', $exception->getMessage()));
        }
    }
}
