<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Reader;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Glue\Merchant\Api\Backend\Exception\MerchantBackendExceptionFactory;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;

class MerchantReader implements MerchantReaderInterface
{
    public function __construct(
        protected MerchantFacadeInterface $merchantFacade,
        protected MerchantBackendExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When no merchant matches the reference.
     */
    public function getMerchantByReference(string $merchantReference): MerchantTransfer
    {
        $merchantTransfer = $this->merchantFacade->findOne(
            (new MerchantCriteriaTransfer())->setMerchantReference($merchantReference),
        );

        if ($merchantTransfer === null) {
            throw $this->exceptionFactory->createMerchantNotFoundException($merchantReference);
        }

        return $merchantTransfer;
    }

    public function getMerchantCollection(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer
    {
        return $this->merchantFacade->get($merchantCriteriaTransfer);
    }
}
