<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model;

use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * Get carrier method
 */
class GetResultMethod
{
    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * @param MethodFactory $methodFactory
     */
    public function __construct(
        MethodFactory $methodFactory
    ) {
        $this->methodFactory = $methodFactory;
    }

    /**
     * Get result method
     *
     * @param AbstractCarrierInterface $carrier
     * @param float $shippingPrice
     * @return Method
     */
    public function execute(
        AbstractCarrierInterface $carrier,
        float $shippingPrice
    ) {
        /** @var Method $method */
        $method = $this->methodFactory->create();

        $method->setData('carrier', $carrier->getCarrierCode());
        $method->setData('method', $carrier->getCarrierCode());

        $method->setData('carrier_title', $carrier->getConfigData('title'));
        $method->setData('method_title', $carrier->getConfigData('name'));

        $method->setData('cost', $shippingPrice);
        $method->setPrice($shippingPrice);

        return $method;
    }
}
