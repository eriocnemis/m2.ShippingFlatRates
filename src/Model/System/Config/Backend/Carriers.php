<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Model\System\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Backend for carriers data
 *
 * @api
 */
class Carriers extends ArraySerialized
{
    /**
     * Format data
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            foreach ($value as $key => $data) {
                /* remove empty data */
                if (empty($data['code'])) {
                    unset($value[$key]);
                    continue;
                }
                /* remove duplicate */
                if ($key != $data['code']) {
                    unset($value[$key]);
                    $value[$data['code']] = $data;
                }
            }
        }

        $this->setValue($value);
        return parent::beforeSave();
    }

    /**
     * Clean cache configuration
     *
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->isValueChanged()) {
            $this->cacheTypeList->cleanType(Config::TYPE_IDENTIFIER);
        }
        return $this;
    }
}
