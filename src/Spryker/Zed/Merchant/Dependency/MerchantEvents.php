<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Dependency;

class MerchantEvents
{
    /**
     * Specification
     * - This events will be used for spy_merchant publishing.
     * - On a merchant update, `EventEntityTransfer.modifiedColumns` lists the changed `spy_merchant` columns (in the format `spy_merchant.<column>`) and `spy_merchant_store.fk_store` when the store relation changed.
     * - A `spy_merchant` column is listed when `MerchantTransfer` holds it and its value differs from the stored value. This includes columns that other modules add to `spy_merchant`.
     * - When none of these changed, `modifiedColumns` contains only {@link \Spryker\Zed\Merchant\MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA}. It does not tell which related data changed.
     * - On a merchant create and on a republish without a merchant update, `modifiedColumns` is empty.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_PUBLISH = 'Merchant.merchant.publish';

    /**
     * Specification
     * - This events will be used for spy_merchant entity changes.
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_MERCHANT_UPDATE = 'Entity.spy_merchant.update';

    /**
     * @var string
     */
    public const MERCHANT_EXPORTED = 'Merchant.exported';

    /**
     * @var string
     */
    public const MERCHANT_CREATED = 'Merchant.created';

    /**
     * @var string
     */
    public const MERCHANT_UPDATED = 'Merchant.updated';
}
