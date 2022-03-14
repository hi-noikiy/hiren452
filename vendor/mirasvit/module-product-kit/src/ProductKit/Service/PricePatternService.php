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
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Service;

class PricePatternService
{
    /**
     * @param string $pattern ##.99
     * @param float  $price
     *
     * @return float
     */
    public function template($pattern, $price)
    {
        $pattern = $this->normalizePattern($pattern);

        $fPrice = number_format($price, 2, '.', '');

        for ($i = 1; $i <= strlen($pattern); $i++) {
            $digit = $pattern[strlen($pattern) - $i];

            if ($digit !== '#') {
                $fPrice[strlen($fPrice) - $i] = $digit;
            }
        }

        return (float)$fPrice;
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function normalizePattern($pattern)
    {
        $pattern = preg_replace('/[^#.0-9]/', '', $pattern);
        $crumbs  = explode('.', $pattern);

        $afterComma = isset($crumbs[1]) ? $crumbs[1] : '##';
        $afterComma = substr($afterComma, 0, 2);
        $afterComma = $afterComma . str_repeat('#', 2 - strlen($afterComma));

        $beforeComma = isset($crumbs[0]) ? $crumbs[0] : '';

        return $beforeComma . '.' . $afterComma;
    }
}