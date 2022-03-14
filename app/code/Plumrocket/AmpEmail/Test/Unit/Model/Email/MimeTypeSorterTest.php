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
use Plumrocket\AmpEmail\Model\Email\AmpMessage;
use \Plumrocket\AmpEmail\Model\Email\MimeTypeSorter;
use Zend\Mime\Mime;

class MimeTypeSorterTest extends TestCase
{
    /**
     * @var MimeTypeSorter
     */
    private $mimeTypeSorter;

    private $parts;

    protected function setUp() //@codingStandardsIgnoreLine
    {
        $this->mimeTypeSorter = (new ObjectManager($this))->getObject(MimeTypeSorter::class);
    }

    /**
     * @dataProvider partsProvider
     *
     * @param array $parts
     * @param array $expected
     */
    public function testSortTwoTypes(array $parts, array $expected)
    {
        $partsForSort = [];

        foreach ($parts as $partType) {
            $partsForSort[$partType] = $this->getParts()[$partType];
        }

        $sortedParts = $this->mimeTypeSorter->sort($partsForSort);

        $this->assertSame($expected, array_keys($sortedParts));
    }

    public function partsProvider()
    {
        yield [
            'parts' => [],
            'expected' => [],
        ];

        yield [
            'parts' => [
                Mime::TYPE_HTML,
            ],
            'expected' => [
                Mime::TYPE_HTML,
            ],
        ];

        yield [
            'parts' => [
                Mime::TYPE_HTML,
                Mime::TYPE_TEXT,
            ],
            'expected' => [
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
        ];

        yield [
            'parts' => [
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
            'expected' => [
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
        ];

        yield [
            'parts' => [
                AmpMessage::TYPE_AMP,
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
            'expected' => [
                Mime::TYPE_TEXT,
                AmpMessage::TYPE_AMP,
                Mime::TYPE_HTML,
            ],
        ];

        yield [
            'parts' => [
                Mime::TYPE_OCTETSTREAM,
                AmpMessage::TYPE_AMP,
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
            'expected' => [
                Mime::TYPE_TEXT,
                AmpMessage::TYPE_AMP,
                Mime::TYPE_HTML,
                Mime::TYPE_OCTETSTREAM,
            ],
        ];

        yield [
            'parts' => [
                Mime::TYPE_OCTETSTREAM,
                AmpMessage::TYPE_AMP,
                Mime::TYPE_TEXT,
                Mime::TYPE_HTML,
            ],
            'expected' => [
                Mime::TYPE_TEXT,
                AmpMessage::TYPE_AMP,
                Mime::TYPE_HTML,
                Mime::TYPE_OCTETSTREAM,
            ],
        ];
    }

    /**
     * @return \Zend\Mime\Part[]
     */
    public function getParts() : array
    {
        if (null === $this->parts) {
            $this->parts = [
                Mime::TYPE_TEXT      => (new \Zend\Mime\Part())->setType(Mime::TYPE_TEXT),
                Mime::TYPE_HTML      => (new \Zend\Mime\Part())->setType(Mime::TYPE_HTML),
                AmpMessage::TYPE_AMP => (new \Zend\Mime\Part())->setType(AmpMessage::TYPE_AMP),
                Mime::TYPE_OCTETSTREAM => (new \Zend\Mime\Part())->setType(), // PDF can has this mime type
            ];
        }

        return $this->parts;
    }
}
