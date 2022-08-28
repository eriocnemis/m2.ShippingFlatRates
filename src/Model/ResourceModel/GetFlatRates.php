<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model\ResourceModel;

use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Config\ConfigSourceInterface;
use Eriocnemis\ShippingFlatRates\Api\GetFlatRatesInterface;

/**
 * Retrieve flat rates config
 */
class GetFlatRates implements GetFlatRatesInterface
{
    /**
     * Configuration path
     */
    private const XML_CARRIERS = 'default/carriers/eriocnemis_flat_rates/carriers';

    /**
     * @var ConfigSourceInterface
     */
    private $source;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var mixed[]
     */
    private $carriers;

    /**
     * Initialize resource
     *
     * @param ConfigSourceInterface $source
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigSourceInterface $source,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->source = $source;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * Retrieve flat rates config
     *
     * @return mixed[]
     */
    public function execute(): array
    {
        if (null === $this->carriers) {
            $value = $this->source->get(self::XML_CARRIERS);
            if (is_string($value)) {
                try {
                    $carriers = (array)$this->serializer->unserialize($value);
                    foreach ($carriers as $data) {
                        $this->carriers[$data['code']] = $data['label'];
                    }
                } catch (\Exception $e) {
                    $this->logger->critical(
                        sprintf(
                            'Failed to unserialize %s config value. The error is: %s',
                            self::XML_CARRIERS,
                            $e->getMessage()
                        )
                    );
                }
            }
        }
        return $this->carriers ?: [];
    }
}
