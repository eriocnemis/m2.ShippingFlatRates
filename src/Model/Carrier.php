<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Psr\Log\LoggerInterface;

/**
 * Flat rate carrier
 *
 * @api
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var CollectRates
     */
    private $collectRates;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param CollectRates $collectRates
     * @param mixed[] $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        CollectRates $collectRates,
        array $data = []
    ) {
        $this->collectRates = $collectRates;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    /**
     * Set carrier code
     *
     * @param string $code
     * @return $this
     */
    public function setId(string $code)
    {
        $this->_code = $code;

        return $this;
    }

    /**
     * Retrieve carrier code
     *
     * @return string
     */
    public function getId()
    {
        return $this->_code;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        return $this->getConfigFlag('active')
            ? $this->collectRates->execute($this, $request)
            : false;
    }

    /**
     * Get allowed shipping methods
     *
     * @return mixed[]
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
