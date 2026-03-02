<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Status;

class MerchantStatusValidator implements MerchantStatusValidatorInterface
{
    /**
     * @var \Spryker\Zed\Merchant\Business\Status\MerchantStatusReaderInterface
     */
    protected $merchantStatusReader;

    public function __construct(
        MerchantStatusReaderInterface $merchantStatusReader
    ) {
        $this->merchantStatusReader = $merchantStatusReader;
    }

    public function isMerchantStatusTransitionValid(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        if (!$this->isTransitionToStatusAllowed($currentStatus, $newStatus)) {
            return false;
        }

        return true;
    }

    protected function isTransitionToStatusAllowed(string $currentStatus, string $newStatus): bool
    {
        $applicableMerchantStatuses = $this->merchantStatusReader->getApplicableMerchantStatuses($currentStatus);

        return in_array($newStatus, $applicableMerchantStatuses);
    }
}
