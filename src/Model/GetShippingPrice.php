<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Calculate shipping price
 */
class GetShippingPrice
{
    /**
     * @var GetFreeBoxesCount
     */
    private $getFreeBoxesCount;

    /**
     * @param GetFreeBoxesCount $getFreeBoxesCount
     */
    public function __construct(
        GetFreeBoxesCount $getFreeBoxesCount
    ) {
        $this->getFreeBoxesCount = $getFreeBoxesCount;
    }

    /**
     * Retrieve shipping price
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

        $configPrice = $this->getConfigPrice($carrier);
        if ($carrier->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $request->getPackageQty() * $configPrice - $freeBoxes * $configPrice;
        } elseif ($carrier->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $configPrice;
        }

        if (null !== $shippingPrice) {
            $shippingPrice = $carrier->getFinalPriceWithHandlingFee($shippingPrice);
            if ($request->getPackageQty() == $freeBoxes) {
                $shippingPrice = 0.00;
            }
        }

        return $shippingPrice;
    }

    /**
     * Retrieve config price
     *
     * @param AbstractCarrierInterface $carrier
     * @return float
     */
    private function getConfigPrice(AbstractCarrierInterface $carrier)
    {
        $configPrice = $carrier->getConfigData('price');
        if (is_string($configPrice)) {
            return (float)$configPrice;
        }
        return 0.00;
    }
}
