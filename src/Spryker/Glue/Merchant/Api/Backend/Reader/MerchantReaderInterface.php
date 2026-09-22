<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Reader;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantReaderInterface
{
    public function getMerchantByReference(string $merchantReference): MerchantTransfer;

    public function getMerchantCollection(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer;
}
