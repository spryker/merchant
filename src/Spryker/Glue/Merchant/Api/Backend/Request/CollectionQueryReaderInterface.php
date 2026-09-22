<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Request;

use Generated\Shared\Transfer\FilterTransfer;
use Symfony\Component\HttpFoundation\Request;

interface CollectionQueryReaderInterface
{
    /**
     * @param array<string, string> $sortableFieldMap Sort field the client may use, mapped to the persistence column it orders by.
     */
    public function getFilterTransfer(Request $request, array $sortableFieldMap): FilterTransfer;
}
