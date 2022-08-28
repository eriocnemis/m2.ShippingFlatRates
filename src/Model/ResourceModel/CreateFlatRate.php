<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model\ResourceModel;

use Magento\Config\Model\Config\Structure;

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
     * Key that contains field type in structure array
     */
    private const TYPE_KEY = Structure::TYPE_KEY;

    /**
     * @var mixed[]
     */
    private $data = [
        'translate' => 'label',
        'type' => 'text',
        'sortOrder' => '10',
        'showInDefault' => '1',
        'showInWebsite' => '1',
        'showInStore' => '1',
        self::TYPE_KEY => 'group',
    ];

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

        if ('active' != $id) {
            $data['depends'] = [
                'fields' => [
                    'active' => $this->getActiveDepend($code)
                ]
            ];
        }

        return $data;
    }

    /**
     * Retrieve depend path
     *
     * @param string $code
     * @return mixed[]
     */
    private function getActiveDepend(string $code)
    {
        return [
            'id' => 'carriers/' . self::GROUP . '/' . $code . '/active',
            'value' => '1',
            self::TYPE_KEY => 'field',
            'dependPath' => ['carriers', self::GROUP, $code, 'active']
        ];
    }
}
