<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Plugin\Config\Model\Config\Structure;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Config\Model\Config\Structure\Converter;
use Eriocnemis\ShippingFlatRates\Api\GetFlatRatesInterface;
use Eriocnemis\ShippingFlatRates\Model\ResourceModel\CreateFlatRate;

/**
 * Config structure converter plugin
 */
class ConverterPlugin
{
    /**
     * Configuration group
     */
    private const GROUP = CreateFlatRate::GROUP;

    /**
     * @var GetFlatRatesInterface
     */
    private $getFlatRates;

    /**
     * @var CreateFlatRate
     */
    private $createFlatRate;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * Initialize plugin
     *
     * @param ArrayManager $arrayManager
     * @param GetFlatRatesInterface $getFlatRates
     * @param CreateFlatRate $createFlatRate
     */
    public function __construct(
        ArrayManager $arrayManager,
        GetFlatRatesInterface $getFlatRates,
        CreateFlatRate $createFlatRate
    ) {
        $this->getFlatRates = $getFlatRates;
        $this->createFlatRate = $createFlatRate;
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
            $flatRate = (array)$this->arrayManager->get($flatratePath, $config, []);
            $defaultFields = $flatRate['children'] ?? [];

            $flatRates = [];
            /** @var string $label */
            foreach ($this->getFlatRates->execute() as $code => $label) {
                $flatRates[$code] = $this->createFlatRate->execute(
                    (array)$defaultFields,
                    $code,
                    $label
                );
            }

            $config = $this->arrayManager->merge(
                $carriersPath,
                $config,
                [
                    'children' => [
                        self::GROUP => [
                            'children' => $flatRates
                        ]
                    ]
                ]
            );
        }

        return $config;
    }
}
