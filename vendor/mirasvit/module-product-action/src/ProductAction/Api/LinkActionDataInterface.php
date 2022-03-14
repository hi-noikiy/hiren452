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

namespace Mirasvit\ProductAction\Api;

interface LinkActionDataInterface extends ActionDataInterface
{
    const TYPE_RELATED_PRODUCTS   = 1;
    const TYPE_UPSELL_PRODUCTS    = 2;
    const TYPE_CROSSSELL_PRODUCTS = 3;

    const TYPE_RELATED_CODE   = 'update_related';
    const TYPE_UPSELL_CODE    = 'update_up_sell';
    const TYPE_CROSSSELL_CODE = 'update_cross_sell';

    /**
     * @param int $type
     *
     * @return LinkActionDataInterface
     */
    public function setLinkType(int $type): LinkActionDataInterface;

    /**
     * @return int
     */
    public function getLinkType(): int;

    /**
     * @param int $type
     *
     * @return LinkActionDataInterface
     */
    public function setDirection(int $type): LinkActionDataInterface;

    /**
     * @return int
     */
    public function getDirection(): int;

    public function setRemoveAll(bool $type): LinkActionDataInterface;

    public function getRemoveAll(): bool;

    /**
     * @param array $ids
     *
     * @return LinkActionDataInterface
     */
    public function setAddProductIds(array $ids): LinkActionDataInterface;

    /**
     * @return array
     */
    public function getAddProductIds(): array;

    /**
     * @param array $ids
     *
     * @return LinkActionDataInterface
     */
    public function setRemoveProductIds(array $ids): LinkActionDataInterface;

    /**
     * @return array
     */
    public function getRemoveProductIds(): array;

    /**
     * @param array $ids
     *
     * @return LinkActionDataInterface
     */
    public function setCopyProductIds(array $ids): LinkActionDataInterface;

    /**
     * @return array
     */
    public function getCopyProductIds(): array;
}
