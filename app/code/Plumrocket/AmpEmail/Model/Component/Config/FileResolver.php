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

namespace Plumrocket\AmpEmail\Model\Component\Config;

use Magento\Framework\Component\DirSearch;
use Magento\Framework\Component\ComponentRegistrar;

class FileResolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var DirSearch
     */
    private $componentDirSearch;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Config\FileIteratorFactory $iteratorFactory
     * @param DirSearch $componentDirSearch
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Config\FileIteratorFactory $iteratorFactory,
        \Magento\Framework\Component\DirSearch $componentDirSearch
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->moduleReader = $moduleReader;
        $this->componentDirSearch = $componentDirSearch;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'global':
                $iterator = $this->moduleReader->getConfigurationFiles($filename);
                break;
            case 'design':
                $themePaths = $this->componentDirSearch->collectFiles(ComponentRegistrar::THEME, 'etc/' . $filename);
                $iterator = $this->iteratorFactory->create($themePaths);
                break;
            default:
                $iterator = $this->iteratorFactory->create([]);
                break;
        }
        return $iterator;
    }
}
