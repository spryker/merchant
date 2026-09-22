<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Request;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Spryker\Glue\Merchant\Api\Backend\Exception\MerchantBackendExceptionFactory;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

class MerchantCriteriaFilterReader implements MerchantCriteriaFilterReaderInterface
{
    protected const string QUERY_PARAM_FILTER = 'filter';

    protected const string FILTER_KEY_PREFIX = 'merchants.';

    protected const string FILTER_IS_ACTIVE = 'isActive';

    protected const string FILTER_STATUSES = 'statuses';

    protected const string FILTER_STORES = 'stores';

    protected const string FILTER_SEARCH_TERM = 'q';

    /**
     * @var array<int, string>
     */
    protected const array SUPPORTED_FILTERS = [
        self::FILTER_IS_ACTIVE,
        self::FILTER_STATUSES,
        self::FILTER_STORES,
        self::FILTER_SEARCH_TERM,
    ];

    /**
     * @var non-empty-string The native `string` type widens the literal, which `explode()` rejects.
     */
    protected const string FILTER_VALUE_SEPARATOR = ',';

    public function __construct(
        protected MerchantBackendExceptionFactory $exceptionFactory,
        protected MerchantFacadeInterface $merchantFacade
    ) {
    }

    public function applyFiltersFromRequest(
        Request $request,
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): MerchantCriteriaTransfer {
        $filters = $this->extractFiltersFromRequest($request);

        if (isset($filters[static::FILTER_IS_ACTIVE])) {
            $merchantCriteriaTransfer->setIsActive($this->readBooleanFilter($filters[static::FILTER_IS_ACTIVE]));
        }

        $searchTerm = $filters[static::FILTER_SEARCH_TERM] ?? null;

        if (is_string($searchTerm) && $searchTerm !== '') {
            $merchantCriteriaTransfer->setSearchTerm($searchTerm);
        }

        foreach ($this->splitFilterValues($filters[static::FILTER_STATUSES] ?? null) as $status) {
            $this->assertKnownMerchantStatus($status);
            $merchantCriteriaTransfer->addStatus($status);
        }

        foreach ($this->splitFilterValues($filters[static::FILTER_STORES] ?? null) as $storeName) {
            $merchantCriteriaTransfer->addStoreName($storeName);
        }

        return $merchantCriteriaTransfer;
    }

    /**
     * Splits a comma-separated filter value, or an array of raw values, into a list of trimmed non-empty strings.
     *
     * @return array<int, string>
     */
    protected function splitFilterValues(mixed $filterValue): array
    {
        if (is_array($filterValue)) {
            return array_values(array_filter(array_map('strval', $filterValue)));
        }

        if (!is_string($filterValue) || trim($filterValue) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(static::FILTER_VALUE_SEPARATOR, $filterValue))));
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When the value is not a boolean.
     */
    protected function readBooleanFilter(mixed $filterValue): bool
    {
        $isActive = filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($isActive === null) {
            throw $this->exceptionFactory->createInvalidFilterValueException(
                static::FILTER_IS_ACTIVE,
                is_scalar($filterValue) ? (string)$filterValue : gettype($filterValue),
            );
        }

        return $isActive;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When the status is not one of the module's supported statuses.
     */
    protected function assertKnownMerchantStatus(string $status): void
    {
        $supportedStatuses = $this->merchantFacade->getMerchantStatuses();

        if (!in_array($status, $supportedStatuses, true)) {
            throw $this->exceptionFactory->createUnknownStatusException($status, $supportedStatuses);
        }
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a filter is outside the allow list.
     *
     * @return array<string, mixed>
     */
    protected function extractFiltersFromRequest(Request $request): array
    {
        $filters = [];

        foreach ($request->query->all(static::QUERY_PARAM_FILTER) as $key => $value) {
            $property = str_starts_with($key, static::FILTER_KEY_PREFIX)
                ? substr($key, strlen(static::FILTER_KEY_PREFIX))
                : $key;

            if (!in_array($property, static::SUPPORTED_FILTERS, true)) {
                throw $this->exceptionFactory->createUnknownFilterException($property, static::SUPPORTED_FILTERS);
            }

            $filters[$property] = $value;
        }

        return $filters;
    }
}
