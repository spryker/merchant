<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Request;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Symfony\Component\HttpFoundation\Request;

interface MerchantCriteriaFilterReaderInterface
{
    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a filter is outside the allow list or has an invalid value.
     */
    public function applyFiltersFromRequest(
        Request $request,
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): MerchantCriteriaTransfer;
}
