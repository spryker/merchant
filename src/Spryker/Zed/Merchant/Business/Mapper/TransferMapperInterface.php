<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Mapper;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface TransferMapperInterface
{
    public function mapTransferDataByAllowedFields(AbstractTransfer $transfer, array $allowedFields): array;
}
