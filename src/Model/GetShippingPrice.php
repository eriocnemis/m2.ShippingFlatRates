<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;

/**
 * Get shipping price
 */
class GetShippingPrice
{
    /**
     * @var ItemPriceCalculator
     */
    private $calculator;

    /**
     * @var GetFreeBoxesCount
     */
    private $getFreeBoxesCount;

    /**
     * @param ItemPriceCalculator $calculator
     * @param GetFreeBoxesCount $getFreeBoxesCount
     */
    public function __construct(
        ItemPriceCalculator $calculator,
        GetFreeBoxesCount $getFreeBoxesCount
    ) {
        $this->calculator = $calculator;
        $this->getFreeBoxesCount = $getFreeBoxesCount;
    }

    /**
     * Returns shipping price
     *
     * @param AbstractCarrierInterface $carrier
     * @param RateRequest $request
     * @return float|null
     */
    public function execute(
        AbstractCarrierInterface $carrier,
        RateRequest $request
    ) {
        $freeBoxes = $this->getFreeBoxesCount->execute($request);
        $shippingPrice = null;

        $configPrice = (float)$carrier->getConfigData('price');
        if ($carrier->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $this->calculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);
        } elseif ($carrier->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $this->calculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);
        }

        if (null !== $shippingPrice) {
            $shippingPrice = $carrier->getFinalPriceWithHandlingFee($shippingPrice);
            if ($request->getPackageQty() == $freeBoxes) {
                $shippingPrice = 0.00;
            }
        }

        return $shippingPrice;
    }
}
