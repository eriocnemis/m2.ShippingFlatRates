<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Api;

/**
 * Retrieve flat rates config
 *
 * @api
 */
interface GetFlatRatesInterface
{
    /**
     * Retrieve flat rates config
     *
     * @return mixed[]
     */
    public function execute(): array;
}
