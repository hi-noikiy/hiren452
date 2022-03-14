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

namespace Plumrocket\AmpEmail\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Plumrocket\AmpEmail\Model\CssMinify;

class CssMinifyTest extends TestCase
{
    /**
     * @var null | \Plumrocket\AmpEmail\Model\CssMinify
     */
    private $model;

    protected function setUp()
    {
        $this->model = (new ObjectManager($this))
            ->getObject(CssMinify::class);
    }

    /**
     * @dataProvider prepareLengthProvider
     *
     * @param $cssContent
     * @param $stringLength
     * @param $expectedResult
     * @throws \ReflectionException
     */
    public function testPrepareLength($cssContent, $stringLength, $expectedResult)
    {
        $testMethod = new \ReflectionMethod(
            CssMinify::class,
            'prepareLength'
        );
        $testMethod->setAccessible(true);

        $this->assertEquals($expectedResult, $testMethod->invoke($this->model, $cssContent, $stringLength));
    }

    /**
     * @return \Generator
     */
    public function prepareLengthProvider()
    {
        yield [
            'cssContent' => '.p{display:flex}',
            'stringLength' => 100,
            'expectedResult' => '.p{display:flex}',
        ];
        yield [
            'cssContent' => '.p{display:flex;justify-content:space-between} .product-view {top:5px}',
            'stringLength' => 50,
            'expectedResult' => ".p{display:flex;justify-content:space-between}\n .product-view {top:5px}",
        ];
    }
}
