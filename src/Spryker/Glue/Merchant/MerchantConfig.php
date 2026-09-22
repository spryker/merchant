<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant;

use Spryker\Glue\Kernel\AbstractBundleConfig;

class MerchantConfig extends AbstractBundleConfig
{
    /**
     * @api
     */
    public const string RESPONSE_CODE_MERCHANT_NOT_FOUND = '1301';

    /**
     * @api
     */
    public const string RESPONSE_CODE_VALIDATION = '1302';

    /**
     * @api
     */
    public const string RESPONSE_CODE_INVALID_SORT_FIELD = '1303';

    /**
     * @api
     */
    public const string RESPONSE_CODE_UNKNOWN_STORE = '1304';

    /**
     * @api
     */
    public const string RESPONSE_CODE_UNKNOWN_LOCALE = '1305';

    /**
     * @api
     */
    public const string RESPONSE_CODE_MULTIPLE_SORT_FIELDS = '1309';

    /**
     * @api
     */
    public const string RESPONSE_CODE_UNKNOWN_FILTER = '1312';

    /**
     * @api
     */
    public const string RESPONSE_CODE_INVALID_FILTER_VALUE = '1313';

    /**
     * @api
     */
    public const string RESPONSE_CODE_WRITE_FAILED = '1314';

    /**
     * @api
     */
    public const string RESPONSE_CODE_MISSING_MERCHANT_URL_LOCALE = '1315';

    /**
     * @api
     */
    public const string RESPONSE_CODE_UNKNOWN_STATUS = '1316';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_MERCHANT_NOT_FOUND = 'Merchant with reference "%s" was not found.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_INVALID_SORT_FIELD = 'Sort field "%s" is not supported. Supported fields: %s.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_UNKNOWN_STORE = 'Store "%s" does not exist.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_UNKNOWN_LOCALE = 'Locale "%s" does not exist.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_MULTIPLE_SORT_FIELDS = 'The merchant collection orders by a single field. Supported fields: %s.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_UNKNOWN_FILTER = 'Filter "%s" is not supported. Supported filters: %s.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_INVALID_FILTER_VALUE = 'Filter "%s" expects a boolean value, got "%s".';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_WRITE_FAILED = 'The merchant could not be written and the reason was not reported.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_MISSING_MERCHANT_URL_LOCALE = 'Merchant URL for locale "%s" is required because it is assigned to one of the merchant\'s stores.';

    /**
     * @api
     */
    public const string RESPONSE_DETAILS_UNKNOWN_STATUS = 'Status "%s" is not supported. Supported statuses: %s.';
}
