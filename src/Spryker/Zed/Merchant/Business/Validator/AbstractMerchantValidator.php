<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Business\Validator;

use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

abstract class AbstractMerchantValidator implements MerchantValidatorInterface
{
    protected function createSuccessfulMerchantResponseTransfer(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        return (new MerchantResponseTransfer())
            ->setIsSuccess(true)
            ->setMerchant($merchantTransfer);
    }

    /**
     * @param array<int, \Generated\Shared\Transfer\MerchantErrorTransfer> $merchantErrorTransfers
     */
    protected function createFailedMerchantResponseTransfer(
        MerchantTransfer $merchantTransfer,
        array $merchantErrorTransfers
    ): MerchantResponseTransfer {
        $merchantResponseTransfer = (new MerchantResponseTransfer())
            ->setIsSuccess(false)
            ->setMerchant($merchantTransfer);

        foreach ($merchantErrorTransfers as $merchantErrorTransfer) {
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }

    protected function createMerchantErrorTransfer(string $message): MerchantErrorTransfer
    {
        return (new MerchantErrorTransfer())
            ->setMessage($message);
    }
}
