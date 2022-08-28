<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Plugin\Config\Model\Config\Structure;

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
     * Initialize plugin
     *
     * @param GetFlatRatesInterface $getFlatRates
     * @param CreateFlatRate $createFlatRate
     */
    public function __construct(
        GetFlatRatesInterface $getFlatRates,
        CreateFlatRate $createFlatRate
    ) {
        $this->getFlatRates = $getFlatRates;
        $this->createFlatRate = $createFlatRate;
    }

    /**
     * Modify system configuration
     *
     * @param Converter $subject
     * @param mixed[] $result
     * @return mixed[]
     */
    public function afterConvert(Converter $subject, array $result)
    {
        $carriers = $result['config']['system']['sections']['carriers']['children'] ?? [];
        $flatRates = $carriers[self::GROUP]['children'] ?? [];
        $defaultFields = $carriers['flatrate']['children'] ?? [];

        foreach ($this->getFlatRates->execute() as $code => $label) {
            $flatRates[$code] = $this->createFlatRate->execute($defaultFields, $code, $label);
        }

        $carriers[self::GROUP]['children'] = $flatRates;
        $result['config']['system']['sections']['carriers']['children'] = $carriers;

        return $result;
    }
}
