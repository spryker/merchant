<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Mapper;

use Generated\Api\Backend\MerchantsBackendResource;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantResourceMapperInterface
{
    public function mapMerchantTransferToMerchantsBackendResource(MerchantTransfer $merchantTransfer): MerchantsBackendResource;
}
