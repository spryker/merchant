<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Writer;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantWriterInterface
{
    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;
}
