<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Merchant\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ShoppingListItemTransfer;

interface MerchantShoppingListItemToItemMapperInterface
{
    public function map(ShoppingListItemTransfer $shoppingListItemTransfer, ItemTransfer $itemTransfer): ItemTransfer;
}
