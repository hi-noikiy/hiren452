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

use Mirasvit\ProductAction\Api\ActionInterface;

abstract class GenericExecutor implements ActionInterface
{
    protected $message = '';

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setSuccessMessage()
    {
        $this->message = (string)__('Action "%1" was processed. ', $this->getLabel());
    }

    public function setQueueMessage()
    {
        $this->message = (string)__('Action "%1" has added to queue. Make sure your cron job is running to process action.', $this->getLabel());
    }
}
