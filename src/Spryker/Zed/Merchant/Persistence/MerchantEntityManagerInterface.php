<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreTransfer;

interface MerchantEntityManagerInterface
{
    public function saveMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    public function createMerchantStore(MerchantTransfer $merchantTransfer, int $idStore): StoreTransfer;

    public function deleteMerchantStore(MerchantTransfer $merchantTransfer, int $idStore): void;
}
