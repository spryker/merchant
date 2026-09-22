<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Request;

use Generated\Shared\Transfer\FilterTransfer;
use Spryker\Glue\Merchant\Api\Backend\Exception\CollectionQueryExceptionFactory;
use Symfony\Component\HttpFoundation\Request;

class CollectionQueryReader implements CollectionQueryReaderInterface
{
    protected const string QUERY_PARAM_SORT = 'sort';

    protected const string SORT_DESCENDING_PREFIX = '-';

    protected const string ORDER_DIRECTION_ASCENDING = 'ASC';

    protected const string ORDER_DIRECTION_DESCENDING = 'DESC';

    /**
     * @var non-empty-string The native `string` type widens the literal, which `explode()` rejects.
     */
    protected const string SORT_FIELD_SEPARATOR = ',';

    public function __construct(protected CollectionQueryExceptionFactory $exceptionFactory)
    {
    }

    /**
     * @param array<string, string> $sortableFieldMap
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a sort field is outside the allow list.
     */
    public function getFilterTransfer(Request $request, array $sortableFieldMap): FilterTransfer
    {
        $filterTransfer = new FilterTransfer();
        $sortField = $this->readSortField($request, $sortableFieldMap);

        if ($sortField === null) {
            return $filterTransfer;
        }

        $isAscending = !str_starts_with($sortField, static::SORT_DESCENDING_PREFIX);
        $field = ltrim($sortField, static::SORT_DESCENDING_PREFIX);

        if (!isset($sortableFieldMap[$field])) {
            throw $this->exceptionFactory->createInvalidSortFieldException($field, array_keys($sortableFieldMap));
        }

        return $filterTransfer
            ->setOrderBy($sortableFieldMap[$field])
            ->setOrderDirection($isAscending ? static::ORDER_DIRECTION_ASCENDING : static::ORDER_DIRECTION_DESCENDING);
    }

    /**
     * @param array<string, string> $sortableFieldMap
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When several sort fields are requested.
     */
    protected function readSortField(Request $request, array $sortableFieldMap): ?string
    {
        $sort = $request->query->get(static::QUERY_PARAM_SORT);

        if (!is_string($sort) || trim($sort) === '') {
            return null;
        }

        $sortFields = array_filter(array_map('trim', explode(static::SORT_FIELD_SEPARATOR, $sort)));

        if (count($sortFields) > 1) {
            throw $this->exceptionFactory->createMultipleSortFieldsException(array_keys($sortableFieldMap));
        }

        return (string)reset($sortFields);
    }
}
