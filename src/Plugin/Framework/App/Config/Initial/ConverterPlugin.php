<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Plugin\Framework\App\Config\Initial;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\Config\Initial\Converter;
use Eriocnemis\ShippingFlatRates\Api\GetFlatRatesInterface;
use Eriocnemis\ShippingFlatRates\Model\Carrier;

/**
 * Config initial converter plugin
 */
class ConverterPlugin
{
    /**
     * @var GetFlatRatesInterface
     */
    private $getFlatRates;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * Initialize plugin
     *
     * @param ArrayManager $arrayManager
     * @param GetFlatRatesInterface $getFlatRates
     */
    public function __construct(
        ArrayManager $arrayManager,
        GetFlatRatesInterface $getFlatRates
    ) {
        $this->getFlatRates = $getFlatRates;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Modify system configuration
     *
     * @param Converter $converter
     * @param mixed[] $config
     * @return mixed[]
     */
    public function afterConvert(Converter $converter, array $config)
    {
        $flatratePath = $this->arrayManager->findPath('flatrate', $config);
        $carriersPath = $this->arrayManager->findPath('carriers', $config);

        if (null !== $flatratePath && null !== $carriersPath) {
            $data = (array)$this->arrayManager->get($flatratePath, $config, []);

            $flatRates = [];
            foreach ($this->getFlatRates->execute() as $code => $label) {
                $data['model'] = Carrier::class;
                $data['title'] = $label;
                /* add new group to config */
                $flatRates[$code] = $data;
            }
            $config = $this->arrayManager->merge($carriersPath, $config, $flatRates);
        }

        return $config;
    }
}
