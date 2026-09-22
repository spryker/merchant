<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Writer;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Glue\Merchant\Api\Backend\Exception\MerchantBackendExceptionFactory;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;

class MerchantWriter implements MerchantWriterInterface
{
    public function __construct(
        protected MerchantFacadeInterface $merchantFacade,
        protected MerchantBackendExceptionFactory $exceptionFactory,
    ) {
    }

    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->extractMerchant($this->merchantFacade->createMerchant($merchantTransfer));
    }

    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->extractMerchant($this->merchantFacade->updateMerchant($merchantTransfer));
    }

    protected function extractMerchant(MerchantResponseTransfer $merchantResponseTransfer): MerchantTransfer
    {
        if (!$merchantResponseTransfer->getIsSuccess()) {
            throw $this->exceptionFactory->createExceptionFromMerchantResponse($merchantResponseTransfer);
        }

        return $merchantResponseTransfer->getMerchantOrFail();
    }
}
