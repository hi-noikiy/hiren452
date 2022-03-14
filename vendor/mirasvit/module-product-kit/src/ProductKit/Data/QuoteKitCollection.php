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



namespace Mirasvit\ProductKit\Data;

use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Service\KitBuilderService;

class QuoteKitCollection
{
    /**
     * @var KitInterface[]
     */
    private $kits = [];

    /**
     * @var mixed|OfferKitItem[][]
     */
    private $kitItemCombinations = [];

    /**
     * @var KitItemInterface[]
     */
    private $kitItems = [];

    /**
     * @var QuoteKit[]
     */
    private $quoteKits = [];

    private $kitBuilderService;

    private $kitRepository;

    private $quoteKitFactory;

    private $quoteKitItemFactory;

    public function __construct(
        QuoteKitFactory $quoteKitFactory,
        QuoteKitItemFactory $quoteKitItemFactory,
        KitBuilderService $kitBuilderService,
        KitRepository $kitRepository
    ) {
        $this->kitBuilderService   = $kitBuilderService;
        $this->kitRepository       = $kitRepository;
        $this->quoteKitFactory     = $quoteKitFactory;
        $this->quoteKitItemFactory = $quoteKitItemFactory;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function update($data)
    {
        $kitId = (int)$data['kit_id'];

        $this->initKit($kitId);
        $this->initQuoteKit($data['kit_info']['hash'], $kitId);

        return $this;
    }

    /**
     * @param int $kitId
     *
     * @return void
     */
    private function initKit($kitId)
    {
        if (!$this->getKit($kitId)) {
            $kit = $this->kitRepository->get($kitId);
            $this->addKit($kit);

            $kitItems = $this->kitRepository->getItemRepository()->getItems($kit);
            foreach ($kitItems as $kitItem) {
                $this->addKitItem($kit, $kitItem);
            }

            $this->buildKitCombinations($kit);
        }
    }

    /**
     * @param string $hash
     * @param int    $kitId
     *
     * @return void
     */
    private function initQuoteKit($hash, $kitId)
    {
        if (!$this->getQuoteKit($hash)) {
            $quoteKit = $this->quoteKitFactory->create();
            $quoteKit->setHash($hash);
            $quoteKit->setKitId($kitId);

            $kit   = $this->getKit($kitId);
            $items = $this->getKitItems($kit);
            foreach ($items as $item) {
                $quoteKitItem = $this->quoteKitItemFactory->create();
                $quoteKitItem->setPosition($item->getPosition());
                $quoteKitItem->setValid($item->isOptional()); // if isOptional then valid by default
                $quoteKit->addItem($quoteKitItem);
            }

            $this->addQuoteKit($quoteKit);
        }
    }

    private function buildKitCombinations(KitInterface $kit)
    {
        if (!isset($this->kitItemCombinations[$kit->getId()])) {
            $kitItems     = $this->getKitItems($kit);
            $combinations = $this->kitBuilderService->getItemCombinations($kitItems);

            $offerCombinations = [];

            foreach ($combinations as $combination) {
                $combinationHash  = [];
                $combinationItems = [];

                $offerItems = $this->kitBuilderService->getOfferItems($kitItems, $combination);

                foreach ($offerItems as $offerItem) {
                    $combinationHash[] = $offerItem->getId();

                    $combinationItems[$offerItem->getId()] = $offerItem;
                }

                $combinationHash = implode('/', $combinationHash);

                $offerCombinations[$combinationHash] = $combinationItems;
            }

            $this->kitItemCombinations[$kit->getId()] = $offerCombinations;
        }
    }

    /**
     * @param KitInterface $kit
     * @param string       $combination
     *
     * @return array|OfferKitItem[]
     */
    public function getKitItemCombination(KitInterface $kit, $combination)
    {
        return isset($this->kitItemCombinations[$kit->getId()][$combination]) ?
            $this->kitItemCombinations[$kit->getId()][$combination] :
            [];
    }

    public function getKits()
    {
        return $this->kits;
    }

    public function getKitItems(KitInterface $kit)
    {
        return $this->kitItems[$kit->getId()];
    }

    public function getQuoteKits()
    {
        return $this->quoteKits;
    }

    /**
     * @param KitInterface $kit
     *
     * @return $this
     */
    public function addKit($kit)
    {
        if (!isset($this->kits[$kit->getId()])) {
            $this->kits[$kit->getId()] = $kit;
        }

        return $this;
    }

    /**
     * @param KitInterface     $kit
     * @param KitItemInterface $kitItem
     *
     * @return $this
     */
    public function addKitItem($kit, $kitItem)
    {
        if (!isset($this->kitItems[$kit->getId()][$kitItem->getPosition()])) {
            $this->kitItems[$kit->getId()][$kitItem->getPosition()] = $kitItem;
        }

        return $this;
    }

    /**
     * @param KitInterface $kit
     * @param int          $position
     *
     * @return KitItemInterface|false
     */
    public function getKitItem($kit, $position)
    {
        return isset($this->kitItems[$kit->getId()][$position])
            ? $this->kitItems[$kit->getId()][$position]
            : false;
    }

    /**
     * @param int $kitId
     *
     * @return bool|KitInterface
     */
    public function getKit($kitId)
    {
        return isset($this->kits[$kitId]) ? $this->kits[$kitId] : false;
    }

    /**
     * @param QuoteKit $kit
     *
     * @return $this
     */
    public function addQuoteKit($kit)
    {
        if (!isset($this->quoteKits[$kit->getHash()])) {
            $this->quoteKits[$kit->getHash()] = $kit;
        }

        return $this;
    }

    /**
     * @param string $hash
     *
     * @return bool|QuoteKit
     */
    public function getQuoteKit($hash)
    {
        return isset($this->quoteKits[$hash]) ? $this->quoteKits[$hash] : false;
    }
}
