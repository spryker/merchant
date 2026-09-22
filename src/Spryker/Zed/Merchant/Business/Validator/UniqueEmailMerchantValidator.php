<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Business\Validator;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface;

class UniqueEmailMerchantValidator extends AbstractMerchantValidator
{
    protected const string MESSAGE_EMAIL_IS_ALREADY_USED = 'Email is already used.';

    public function __construct(protected MerchantRepositoryInterface $merchantRepository)
    {
    }

    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $email = $merchantTransfer->getEmail();

        if ($email === null || $email === '') {
            return $this->createSuccessfulMerchantResponseTransfer($merchantTransfer);
        }

        $existingMerchantTransfer = $this->merchantRepository->findOne(
            (new MerchantCriteriaTransfer())->setEmail($email),
        );

        if ($existingMerchantTransfer === null) {
            return $this->createSuccessfulMerchantResponseTransfer($merchantTransfer);
        }

        if ($existingMerchantTransfer->getIdMerchant() === $merchantTransfer->getIdMerchant()) {
            return $this->createSuccessfulMerchantResponseTransfer($merchantTransfer);
        }

        return $this->createFailedMerchantResponseTransfer(
            $merchantTransfer,
            [$this->createMerchantErrorTransfer(static::MESSAGE_EMAIL_IS_ALREADY_USED)],
        );
    }
}
