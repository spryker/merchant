<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Business\Validator;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToUrlFacadeInterface;
use Spryker\Zed\Merchant\MerchantConfig;

class UrlMerchantValidator extends AbstractMerchantValidator
{
    protected const string MESSAGE_URL_IS_BLANK = 'Merchant URL for locale "%s" must not be blank.';

    protected const string MESSAGE_URL_IS_TOO_LONG = 'Merchant URL "%s" is longer than %d characters.';

    protected const string MESSAGE_URL_IS_ALREADY_TAKEN = 'Provided URL "%s" is already taken.';

    public function __construct(
        protected MerchantToUrlFacadeInterface $urlFacade,
        protected MerchantConfig $merchantConfig
    ) {
    }

    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantErrorTransfers = [];

        foreach ($merchantTransfer->getUrlCollection() as $urlTransfer) {
            $merchantErrorTransfers = [
                ...$merchantErrorTransfers,
                ...$this->validateUrl($urlTransfer, $merchantTransfer->getIdMerchant()),
            ];
        }

        if ($merchantErrorTransfers === []) {
            return $this->createSuccessfulMerchantResponseTransfer($merchantTransfer);
        }

        return $this->createFailedMerchantResponseTransfer($merchantTransfer, $merchantErrorTransfers);
    }

    /**
     * @return array<int, \Generated\Shared\Transfer\MerchantErrorTransfer>
     */
    protected function validateUrl(UrlTransfer $urlTransfer, ?int $idMerchant): array
    {
        $url = $urlTransfer->getUrl();

        if ($url === null || $url === '') {
            return [$this->createMerchantErrorTransfer(
                sprintf(static::MESSAGE_URL_IS_BLANK, (string)$urlTransfer->getLocaleName()),
            )];
        }

        $maxLength = $this->merchantConfig->getMerchantUrlMaxLength();

        if (mb_strlen($url) > $maxLength) {
            return [$this->createMerchantErrorTransfer(
                sprintf(static::MESSAGE_URL_IS_TOO_LONG, $url, $maxLength),
            )];
        }

        if (!$this->isUrlTaken($url, $idMerchant)) {
            return [];
        }

        return [$this->createMerchantErrorTransfer(
            sprintf(static::MESSAGE_URL_IS_ALREADY_TAKEN, $url),
        )];
    }

    protected function isUrlTaken(string $url, ?int $idMerchant): bool
    {
        $existingUrlTransfer = $this->urlFacade->findUrlCaseInsensitive((new UrlTransfer())->setUrl($url));

        if ($existingUrlTransfer === null) {
            return false;
        }

        $existingIdMerchant = $existingUrlTransfer->getFkResourceMerchant();

        return $existingIdMerchant === null || $existingIdMerchant !== $idMerchant;
    }
}
