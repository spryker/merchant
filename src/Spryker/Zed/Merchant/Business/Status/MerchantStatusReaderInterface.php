<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Status;

interface MerchantStatusReaderInterface
{
    /**
     * @param string $currentStatus
     *
     * @return array<string>
     */
    public function getApplicableMerchantStatuses(string $currentStatus): array;
}
