<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Business\Validator;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

class MerchantValidator implements MerchantValidatorInterface
{
    /**
     * @param array<\Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantValidatorPluginInterface> $merchantValidatorPlugins
     */
    public function __construct(protected array $merchantValidatorPlugins)
    {
    }

    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = (new MerchantResponseTransfer())
            ->setIsSuccess(true)
            ->setMerchant($merchantTransfer);

        foreach ($this->merchantValidatorPlugins as $merchantValidatorPlugin) {
            $merchantResponseTransfer = $this->mergeValidationResult(
                $merchantResponseTransfer,
                $merchantValidatorPlugin->validate($merchantTransfer),
            );
        }

        return $merchantResponseTransfer;
    }

    protected function mergeValidationResult(
        MerchantResponseTransfer $merchantResponseTransfer,
        MerchantResponseTransfer $validationMerchantResponseTransfer
    ): MerchantResponseTransfer {
        if ($validationMerchantResponseTransfer->getIsSuccess()) {
            return $merchantResponseTransfer;
        }

        $merchantResponseTransfer->setIsSuccess(false);

        foreach ($validationMerchantResponseTransfer->getErrors() as $merchantErrorTransfer) {
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }
}
