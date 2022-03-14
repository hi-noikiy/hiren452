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

namespace Mirasvit\ProductAction\Action;

use Magento\Framework\MessageQueue\PublisherInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;
use Mirasvit\ProductAction\Api\MetaProviderInterface;

class GenericAction extends GenericExecutor
{
    private $actionDataFactory;

    private $actionModel;

    private $label;

    private $code;

    private $ajaxMode;

    private $messagePublisher;

    private $metaProvider;

    private $executor;

    public function __construct(
        GenericActionDataFactory $actionDataFactory,
        string $label,
        string $code,
        MetaProviderInterface $metaProvider,
        ExecutorInterface $executor,
        ActionDataInterface $actionModel,
        PublisherInterface $messagePublisher,
        bool $ajaxMode = false
    ) {
        $this->actionDataFactory = $actionDataFactory;
        $this->label             = $label;
        $this->code              = $code;
        $this->ajaxMode          = $ajaxMode;
        $this->metaProvider      = $metaProvider;
        $this->executor          = $executor;
        $this->actionModel       = $actionModel;
        $this->messagePublisher  = $messagePublisher;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isAjaxMode(): bool
    {
        return $this->ajaxMode;
    }

    public function getMeta(): array
    {
        return $this->metaProvider->getMeta();
    }

    public function getExecutor(): ExecutorInterface
    {
        return $this->executor;
    }

    public function process(array $ids, array $params): void
    {
        $this->getActionData($ids, $params);

        $isQueue = isset($params[MetaProviderInterface::PARAM_IS_ENQUEUE]) ? (bool)$params[MetaProviderInterface::PARAM_IS_ENQUEUE] : false;

        if ($isQueue) {
            $this->enqueue($this->actionModel);
            $this->setQueueMessage();
        } else {
            $this->getExecutor()->execute($this->actionModel);
            $this->setSuccessMessage();
        }
    }

    public function enqueue(ActionDataInterface $actionData): void
    {
        $this->messagePublisher->publish('mst_product_action.process', $actionData);
    }

    public function getActionData(array $ids, array $params): void
    {
        $params[MetaProviderInterface::PARAM_CODE]  = $this->getCode();
        $params[MetaProviderInterface::PARAM_IDS]   = $ids;
        $params[MetaProviderInterface::PARAM_CLASS] = get_class($this->actionModel);

        $this->actionModel->setData($params);
        $this->actionModel->setActionData(SerializeService::encode($params));
    }
}
