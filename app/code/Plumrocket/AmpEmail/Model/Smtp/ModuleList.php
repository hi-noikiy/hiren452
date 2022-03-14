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

namespace Plumrocket\AmpEmail\Model\Smtp;

class ModuleList
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ModuleList constructor.
     *
     * @param \Magento\Framework\Module\Manager                  $moduleManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isOnlyZendOne() : bool
    {
        $onlyZendOne = false;

        foreach ($this->getListOfModulesThatSupportOnlyZendOne() as $moduleInfo) {
            if ($this->moduleManager->isEnabled($moduleInfo['name'])
                && (! $moduleInfo['path'] || $this->scopeConfig->isSetFlag($moduleInfo['path']))
            ) {
                $onlyZendOne = true;
            }
        }

        return $onlyZendOne;
    }

    /**
     * @return array
     */
    private function getListOfModulesThatSupportOnlyZendOne() : array
    {
        return [
//            [
//                'name' => 'Company_ModuleName',
//                'path' => 'vendor/general/enabled',
//            ],
        ];
    }
}
