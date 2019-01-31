<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\MerchantAddressTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantEntityManagerInterface
{
    /**
     * @param int $idMerchant
     *
     * @return void
     */
    public function deleteMerchantById(int $idMerchant): void;

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function saveMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantAddressTransfer $merchantAddressTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantAddressTransfer
     */
    public function saveMerchantAddress(MerchantAddressTransfer $merchantAddressTransfer): MerchantAddressTransfer;
}
