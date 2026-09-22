<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Mapper;

use Generated\Api\Backend\MerchantsBackendResource;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Service\Serializer\SerializerServiceInterface;

class MerchantResourceMapper implements MerchantResourceMapperInterface
{
    protected const string RESOURCE_KEY_STORES = 'stores';

    protected const string RESOURCE_KEY_MERCHANT_URLS = 'merchantUrls';

    protected const string RESOURCE_KEY_WAREHOUSES = 'warehouses';

    protected const string RESOURCE_KEY_URL_LOCALE_NAME = 'localeName';

    protected const string RESOURCE_KEY_URL = 'url';

    public function __construct(protected SerializerServiceInterface $serializer)
    {
    }

    public function mapMerchantTransferToMerchantsBackendResource(MerchantTransfer $merchantTransfer): MerchantsBackendResource
    {
        /** @var \Generated\Api\Backend\MerchantsBackendResource $merchantsBackendResource */
        $merchantsBackendResource = $this->serializer->denormalize(
            $this->mapMerchantTransferToResourceData($merchantTransfer),
            MerchantsBackendResource::class,
        );

        return $merchantsBackendResource;
    }

    /**
     * Keys are the resource's own attribute names, expanded with the derived stores/merchantUrls/warehouses arrays.
     *
     * @return array<string, mixed>
     */
    protected function mapMerchantTransferToResourceData(MerchantTransfer $merchantTransfer): array
    {
        $resourceData = $merchantTransfer->toArray(false, true);

        $resourceData[static::RESOURCE_KEY_STORES] = $this->extractStoreNames($merchantTransfer);
        $resourceData[static::RESOURCE_KEY_MERCHANT_URLS] = $this->extractMerchantUrls($merchantTransfer);
        $resourceData[static::RESOURCE_KEY_WAREHOUSES] = $this->extractWarehouseNames($merchantTransfer);

        return $resourceData;
    }

    /**
     * The store names the merchant is assigned to.
     *
     * @return array<int, string>
     */
    protected function extractStoreNames(MerchantTransfer $merchantTransfer): array
    {
        $storeRelationTransfer = $merchantTransfer->getStoreRelation();

        if ($storeRelationTransfer === null) {
            return [];
        }

        $storeNames = [];

        foreach ($storeRelationTransfer->getStores() as $storeTransfer) {
            $storeName = $storeTransfer->getName();

            if ($storeName !== null) {
                $storeNames[] = $storeName;
            }
        }

        return $storeNames;
    }

    /**
     * Each entry has `localeName` and `url` keys.
     *
     * @return array<int, array<string, string|null>>
     */
    protected function extractMerchantUrls(MerchantTransfer $merchantTransfer): array
    {
        $merchantUrls = [];

        foreach ($merchantTransfer->getUrlCollection() as $urlTransfer) {
            $merchantUrls[] = [
                static::RESOURCE_KEY_URL_LOCALE_NAME => $urlTransfer->getLocaleName(),
                static::RESOURCE_KEY_URL => $urlTransfer->getUrl(),
            ];
        }

        return $merchantUrls;
    }

    /**
     * The warehouse names assigned to the merchant.
     *
     * @return array<int, string>
     */
    protected function extractWarehouseNames(MerchantTransfer $merchantTransfer): array
    {
        $warehouseNames = [];

        foreach ($merchantTransfer->getStocks() as $stockTransfer) {
            $stockName = $stockTransfer->getName();

            if ($stockName !== null) {
                $warehouseNames[] = $stockName;
            }
        }

        return $warehouseNames;
    }
}
