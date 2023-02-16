<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model\ResourceModel;

/**
 * Create flat rate
 */
class CreateFlatRate
{
    /**
     * Configuration group
     */
    public const GROUP = 'eriocnemis_flat_rates';

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
            'path' => 'carriers/' . self::GROUP
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
        foreach ($fields as $id => $data) {
            $children[$id] = $this->getChild($id, $code, $data);
        }

        return $children;
    }

    /**
     * Retrieve configuration of child
     *
     * @param string $id
     * @param string $code
     * @param mixed[] $data
     * @return mixed[]
     * @todo nide add dept ship to specific countries field
     */
    private function getChild(string $id, string $code, array $data): array
    {
        $data['path'] = 'carriers/' . self::GROUP . '/' . $code;
        $data['config_path'] = 'carriers/' . $code . '/' . $id;

        return $data;
    }
}
