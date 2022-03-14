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

namespace Plumrocket\AmpEmail\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var \Plumrocket\AmpEmail\Model\AddAmpAlternativesToTemplatesInterface
     */
    private $addAmpAlternativesToTemplates;

    /**
     * RecurringData constructor.
     *
     * @param \Plumrocket\AmpEmail\Model\AddAmpAlternativesToTemplatesInterface $addAmpAlternativesToTemplates
     */
    public function __construct(
        \Plumrocket\AmpEmail\Model\AddAmpAlternativesToTemplatesInterface $addAmpAlternativesToTemplates
    ) {
        $this->addAmpAlternativesToTemplates = $addAmpAlternativesToTemplates;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addAmpAlternativesToTemplates->execute();
    }
}
