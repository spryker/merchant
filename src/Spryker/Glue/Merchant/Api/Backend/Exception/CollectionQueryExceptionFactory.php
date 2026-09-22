<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Exception;

use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\Merchant\MerchantConfig;
use Symfony\Component\HttpFoundation\Response;

class CollectionQueryExceptionFactory
{
    /**
     * @param array<int, string> $sortableFields
     */
    public function createInvalidSortFieldException(string $sortField, array $sortableFields): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MerchantConfig::RESPONSE_CODE_INVALID_SORT_FIELD,
            sprintf(
                MerchantConfig::RESPONSE_DETAILS_INVALID_SORT_FIELD,
                $sortField,
                implode(', ', $sortableFields),
            ),
        );
    }

    /**
     * @param array<int, string> $sortableFields
     */
    public function createMultipleSortFieldsException(array $sortableFields): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MerchantConfig::RESPONSE_CODE_MULTIPLE_SORT_FIELDS,
            sprintf(
                MerchantConfig::RESPONSE_DETAILS_MULTIPLE_SORT_FIELDS,
                implode(', ', $sortableFields),
            ),
        );
    }
}
