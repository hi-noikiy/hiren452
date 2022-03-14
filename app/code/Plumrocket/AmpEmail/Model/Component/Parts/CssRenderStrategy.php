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

namespace Plumrocket\AmpEmail\Model\Component\Parts;

class CssRenderStrategy implements \Plumrocket\AmpEmail\Model\ComponentPartRenderStrategyInterface
{
    const TYPE = 'css';

    const CSS_PART_PLACEHOLDER = '<style amp-custom>';

    /**
     * @var \Magento\Framework\Code\Minifier\AdapterInterface
     */
    private $adapter;

    /**
     * CssRenderStrategy constructor.
     *
     * @param \Magento\Framework\Code\Minifier\AdapterInterface $adapter
     */
    public function __construct(\Magento\Framework\Code\Minifier\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param array  $partContents
     * @param string $emailContent
     * @return string
     */
    public function render(array $partContents, string $emailContent) : string
    {
        $css = '';
        foreach ($partContents as $partContent) {
            /**
             * trim copyright message
             */
            if (preg_match('#\/\*[\w\W]+?\*\/#m', $partContent, $matches)
                && strpos($matches[0], 'Copyright') !== false
            ) {
                $partContent = str_replace($matches[0], '', $partContent);
            }

            $css .= $partContent;
        }

        $cssAdapter = $this->adapter;
        $minifyCss = static function ($matches) use ($css, $cssAdapter) {
            return isset($matches[1])
                ? "<style amp-custom>\n" . $cssAdapter->minify($css . $matches[1]) . "\n</style>"
                : $matches[0];
        };

        return preg_replace_callback('#<style amp-custom>([\S\s]*?)<\/style>#U', $minifyCss, $emailContent);
    }
}
