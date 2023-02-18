<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model\ResourceModel;

/**
 * Create flat rate data
 */
class CreateFlatRate
{
    /**
     * Configuration group
     */
    public const GROUP = 'eriocnemis_flat_rates';

    /**
     * Default delimiter for path
     */
    private const DELIMITER = '/';

    /**
     * @var mixed[]
     */
    private $data = [];

    /**
     * Initialize resource
     *
     * @param mixed[] $data
     */
    public function __construct(
        array $data
    ) {
        $this->data = $data;
    }

    /**
     * Create flat rate configuration
     *
     * @param mixed[] $fields
     * @param string $code
     * @param string $label
     * @return mixed[]
     */
    public function execute(array $fields, string $code, string $label): array
    {
        $data = [
            'id' => $code,
            'label' => __($label),
            'children' => $this->getChildren($fields, $code),
            'path' => 'carriers' . self::DELIMITER . self::GROUP
        ];

        return array_merge($this->data, $data);
    }

    /**
     * Retrieve configuration of children
     *
     * @param mixed[] $fields
     * @param string $code
     * @return mixed[]
     */
    private function getChildren(array $fields, string $code): array
    {
        $children = [];
        /** @var mixed[] $data */
        foreach ($fields as $fieldId => $data) {
            $children[$fieldId] = $this->getChild($fieldId, $code, $data);
        }

        return $children;
    }

    /**
     * Retrieve configuration of child
     *
     * @param string $fieldId
     * @param string $code
     * @param mixed[] $data
     * @return mixed[]
     * @todo nide add dept ship to specific countries field
     */
    private function getChild(string $fieldId, string $code, array $data): array
    {
        $data['path'] = 'carriers' . self::DELIMITER . self::GROUP . self::DELIMITER . $code;
        $data['config_path'] = 'carriers' . self::DELIMITER . $code . self::DELIMITER . $fieldId;

        return $data;
    }
}
