<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Exception;

use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\Merchant\MerchantConfig;
use Symfony\Component\HttpFoundation\Response;

class MerchantBackendExceptionFactory
{
    public function createMerchantNotFoundException(string $merchantReference): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            MerchantConfig::RESPONSE_CODE_MERCHANT_NOT_FOUND,
            sprintf(MerchantConfig::RESPONSE_DETAILS_MERCHANT_NOT_FOUND, $merchantReference),
        );
    }

    public function createUnknownStoreException(string $storeName): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            MerchantConfig::RESPONSE_CODE_UNKNOWN_STORE,
            sprintf(MerchantConfig::RESPONSE_DETAILS_UNKNOWN_STORE, $storeName),
        );
    }

    public function createUnknownLocaleException(string $localeName): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            MerchantConfig::RESPONSE_CODE_UNKNOWN_LOCALE,
            sprintf(MerchantConfig::RESPONSE_DETAILS_UNKNOWN_LOCALE, $localeName),
        );
    }

    public function createMissingMerchantUrlLocaleException(string $localeName): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            MerchantConfig::RESPONSE_CODE_MISSING_MERCHANT_URL_LOCALE,
            sprintf(MerchantConfig::RESPONSE_DETAILS_MISSING_MERCHANT_URL_LOCALE, $localeName),
        );
    }

    public function createExceptionFromMerchantResponse(MerchantResponseTransfer $merchantResponseTransfer): GlueApiException
    {
        $merchantErrorTransfers = $this->extractErrors($merchantResponseTransfer);

        if ($merchantErrorTransfers === []) {
            return $this->createWriteFailedException();
        }

        $glueApiException = new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            MerchantConfig::RESPONSE_CODE_VALIDATION,
            (string)$merchantErrorTransfers[0]->getMessage(),
        );

        if (count($merchantErrorTransfers) > 1) {
            $glueApiException->setErrors($this->mapMerchantErrorsToErrors($merchantErrorTransfers));
        }

        return $glueApiException;
    }

    public function createWriteFailedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            MerchantConfig::RESPONSE_CODE_WRITE_FAILED,
            MerchantConfig::RESPONSE_DETAILS_WRITE_FAILED,
        );
    }

    /**
     * @param array<int, string> $supportedFilters
     */
    public function createUnknownFilterException(string $filter, array $supportedFilters): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MerchantConfig::RESPONSE_CODE_UNKNOWN_FILTER,
            sprintf(MerchantConfig::RESPONSE_DETAILS_UNKNOWN_FILTER, $filter, implode(', ', $supportedFilters)),
        );
    }

    public function createInvalidFilterValueException(string $filter, string $value): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MerchantConfig::RESPONSE_CODE_INVALID_FILTER_VALUE,
            sprintf(MerchantConfig::RESPONSE_DETAILS_INVALID_FILTER_VALUE, $filter, $value),
        );
    }

    /**
     * @param array<int, string> $supportedStatuses
     */
    public function createUnknownStatusException(string $status, array $supportedStatuses): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MerchantConfig::RESPONSE_CODE_UNKNOWN_STATUS,
            sprintf(MerchantConfig::RESPONSE_DETAILS_UNKNOWN_STATUS, $status, implode(', ', $supportedStatuses)),
        );
    }

    /**
     * @return array<int, \Generated\Shared\Transfer\MerchantErrorTransfer>
     */
    protected function extractErrors(MerchantResponseTransfer $merchantResponseTransfer): array
    {
        $merchantErrorTransfers = [];

        foreach ($merchantResponseTransfer->getErrors() as $merchantErrorTransfer) {
            $message = $merchantErrorTransfer->getMessage();

            if ($message !== null && $message !== '') {
                $merchantErrorTransfers[] = $merchantErrorTransfer;
            }
        }

        return $merchantErrorTransfers;
    }

    /**
     * @param array<int, \Generated\Shared\Transfer\MerchantErrorTransfer> $merchantErrorTransfers
     *
     * @return array<int, array{code: string, status: int, detail: string}>
     */
    protected function mapMerchantErrorsToErrors(array $merchantErrorTransfers): array
    {
        return array_map(fn (MerchantErrorTransfer $merchantErrorTransfer): array => [
            'code' => MerchantConfig::RESPONSE_CODE_VALIDATION,
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'detail' => (string)$merchantErrorTransfer->getMessage(),
        ], $merchantErrorTransfers);
    }
}
