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
use Plumrocket\AmpEmail\Model\Template\ClearAmpForEmailHtml;

class ClearAmpForEmailHtmlTest extends TestCase
{
    /**
     * @var ClearAmpForEmailHtml
     */
    private $clearAmpForEmailHtml;

    protected function setUp()
    {
        $this->clearAmpForEmailHtml = (new ObjectManager($this))->getObject(ClearAmpForEmailHtml::class);
    }

    /**
     * @dataProvider emailFileContentsProvider
     *
     * @param string $emailFileContent
     * @param string $expected
     */
    public function testClearTelHref(string $emailFileContent, string $expected)
    {
        $this->assertSame($expected, $this->clearAmpForEmailHtml->execute($emailFileContent));
    }

    public function testNothingToDo()
    {
        $testContent = 'just text with simple html <span>for email</span>';
        $this->assertSame($testContent, $this->clearAmpForEmailHtml->execute($testContent));
    }

    /**
     * @return \Generator
     */
    public function emailFileContentsProvider()
    {
        yield [
            'emailFileContent' => '<a href="tel:(555) 229-3326">(555) 229-3326</a>',
            'expected' => '<span>(555) 229-3326</span>'
        ];

        yield [
            'emailFileContent' => '<a
href="tel:(555) 229-3326"
class="test-class"
>
(555) 229-3326
</a>',
            'expected' => '<span>
(555) 229-3326
</span>'
        ];

        yield [
            'emailFileContent' => '<a href="tel:(555) 229-3326">
(555) 229-3326
</a>',
            'expected' => '<span>
(555) 229-3326
</span>'
        ];

        yield [
            'emailFileContent' => '
Calder,  Michigan, 49628-7978<br />
United States<br />
T: <a href="tel:(555) 229-3326">(555) 229-3326</a>

</p>
    </div>
    </div>

    <div>
        <h3>Bill To:</h3>
        <p>Veronica Costello<br />
            ',
            'expected' => '
Calder,  Michigan, 49628-7978<br />
United States<br />
T: <span>(555) 229-3326</span>

</p>
    </div>
    </div>

    <div>
        <h3>Bill To:</h3>
        <p>Veronica Costello<br />
            ',
        ];

        yield [
            'emailFileContent' => '
<a class="logo" href="https://test.com/logo.png">Logo</a>
Calder,  Michigan, 49628-7978<br />
United States<br />
T: <a href="tel:(555) 229-3326">(555) 229-3326</a>

</p>
    </div>
    </div>

    <div>
        <h3>Bill To:</h3>
        <p>Veronica Costello<br />
            ',
            'expected' => '
<a class="logo" href="https://test.com/logo.png">Logo</a>
Calder,  Michigan, 49628-7978<br />
United States<br />
T: <span>(555) 229-3326</span>

</p>
    </div>
    </div>

    <div>
        <h3>Bill To:</h3>
        <p>Veronica Costello<br />
            ',
        ];
    }
}
