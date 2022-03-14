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

namespace Plumrocket\AmpEmail\Test\Unit\Model\Email;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\AmpEmail\Model\Template\Divider;

class DividerTest extends TestCase
{
    /**
     * @var Divider
     */
    private $divider;

    protected function setUp() //@codingStandardsIgnoreLine
    {
        $this->divider = (new ObjectManager($this))->getObject(Divider::class);
    }

    /**
     * @dataProvider emailFileContentsProvider
     *
     * @param string $emailFileContent
     * @param array  $expected
     */
    public function testDivide(string $emailFileContent, array $expected)
    {
        $this->assertSame($expected, $this->divider->divideIntoParts($emailFileContent));
    }

    /**
     * @return \Generator
     */
    public function emailFileContentsProvider()
    {
        yield [
            'emailFileContent' => $this->getTemplateText(),
            'expected' => [
                'content' => $this->getTemplateText(true),
                'styles' => $this->getStylesString(),
            ],
        ];

        yield [
            'emailFileContent' => $this->getTemplateText(true),
            'expected' => [
                'content' => $this->getTemplateText(true),
                'styles' => '',
            ],
        ];

        yield [
            'emailFileContent' => '',
            'expected' => [
                'content' => '',
                'styles' => '',
            ],
        ];
    }

    /**
     * @param bool $empty
     * @return string
     */
    public function getTemplateText(bool $empty = false) : string
    {
        if ($empty) {
            return '<amp-state id="response">
    <script type="application/json">
        {
            "form": {
                "status": ""
            }
        }
    </script>
</amp-state>';
        }

        return '<!--@pramp-styles-start@-->
<style>' .  $this->getStylesString() . '</style>
<!--@pramp-styles-end@--><amp-state id="response">
    <script type="application/json">
        {
            "form": {
                "status": ""
            }
        }
    </script>
</amp-state>';
    }

    /**
     * @return string
     */
    public function getStylesString() : string
    {
        return '.amp-form.show-already-done,
    .already-done.show-amp-form {
        display: none;
    }';
    }
}
