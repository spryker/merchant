<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\MerchantUrlSaver;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantUrlSaverInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function saveMerchantUrls(
        MerchantTransfer $merchantTransfer
    ): MerchantTransfer;
}
