<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Resolver;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantModifiedColumnsResolverInterface
{
    /**
     * @return array<string>
     */
    public function resolveModifiedColumns(MerchantTransfer $existingMerchantTransfer, MerchantTransfer $merchantTransfer): array;
}
