<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Status;

interface MerchantStatusValidatorInterface
{
    public function isMerchantStatusTransitionValid(string $currentStatus, string $newStatus): bool;
}
