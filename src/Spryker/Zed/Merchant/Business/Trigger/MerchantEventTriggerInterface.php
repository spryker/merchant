<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Trigger;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantEventTriggerInterface
{
    public function triggerMerchantCreatedEvent(MerchantTransfer $merchantTransfer): void;

    public function triggerMerchantUpdatedEvent(MerchantTransfer $merchantTransfer): void;
}
