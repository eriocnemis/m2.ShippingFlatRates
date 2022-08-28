<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Rate\Result;

/**
 * Collect shipping rates
 */
class CollectRates
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var GetResultMethod
     */
    private $getResultMethod;

    /**
     * @var GetShippingPrice
     */
    private $getShippingPrice;

    /**
     * @param ResultFactory $resultFactory
     * @param GetResultMethod $getResultMethod
     * @param GetShippingPrice $getShippingPrice
     */
    public function __construct(
        ResultFactory $resultFactory,
        GetResultMethod $getResultMethod,
        GetShippingPrice $getShippingPrice
    ) {
        $this->resultFactory = $resultFactory;
        $this->getResultMethod = $getResultMethod;
        $this->getShippingPrice = $getShippingPrice;
    }

    /**
     * Collect and get rates
     *
     * @param AbstractCarrierInterface $carrier
     * @param RateRequest $request
     * @return Result
     */
    public function execute(
        AbstractCarrierInterface $carrier,
        RateRequest $request
    ) {
        /** @var Result $result */
        $result = $this->resultFactory->create();
        $shippingPrice = $this->getShippingPrice->execute($carrier, $request);

        if (null !== $shippingPrice) {
            $method = $this->getResultMethod->execute($carrier, $shippingPrice);
            $result->append($method);
        }

        return $result;
    }
}
