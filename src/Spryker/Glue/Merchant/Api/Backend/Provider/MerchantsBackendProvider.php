<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Provider;

use Generated\Api\Backend\MerchantsBackendResource;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractBackendProvider;
use Spryker\Glue\Merchant\Api\Backend\Mapper\MerchantResourceMapperInterface;
use Spryker\Glue\Merchant\Api\Backend\Reader\MerchantReaderInterface;
use Spryker\Glue\Merchant\Api\Backend\Request\CollectionQueryReaderInterface;
use Spryker\Glue\Merchant\Api\Backend\Request\MerchantCriteriaFilterReaderInterface;
use Spryker\Service\Container\Attributes\Plugins;

class MerchantsBackendProvider extends AbstractBackendProvider
{
    protected const string URI_VARIABLE_MERCHANT_REFERENCE = 'merchantReference';

    /**
     * Keys are the sort fields a collection request may use; anything outside them is unsupported.
     *
     * @var array<string, string>
     */
    protected const array SORT_FIELD_MAP = [
        'name' => MerchantTransfer::NAME,
        'merchantReference' => MerchantTransfer::MERCHANT_REFERENCE,
        'status' => MerchantTransfer::STATUS,
    ];

    protected const int DEFAULT_COUNT = 0;

    /**
     * @param array<\Spryker\Glue\MerchantExtension\Dependency\Plugin\MerchantBackendResourceExpanderPluginInterface> $merchantBackendResourceExpanderPlugins
     */
    public function __construct(
        protected MerchantReaderInterface $merchantReader,
        protected CollectionQueryReaderInterface $collectionQueryReader,
        protected MerchantCriteriaFilterReaderInterface $merchantCriteriaFilterReader,
        protected MerchantResourceMapperInterface $merchantResourceMapper,
        #[Plugins(dependencyProviderMethod: 'getMerchantBackendResourceExpanderPlugins')]
        protected array $merchantBackendResourceExpanderPlugins = [],
    ) {
    }

    protected function provideItem(): ?object
    {
        return $this->buildMerchantsBackendResource(
            $this->merchantReader->getMerchantByReference(
                (string)$this->getUriVariable(static::URI_VARIABLE_MERCHANT_REFERENCE),
            ),
        );
    }

    /**
     * @return array<\Generated\Api\Backend\MerchantsBackendResource>
     */
    protected function provideCollection(): array
    {
        $merchantCriteriaTransfer = $this->buildMerchantCriteriaTransfer();
        $merchantCollectionTransfer = $this->merchantReader->getMerchantCollection($merchantCriteriaTransfer);

        $merchantsBackendResources = [];

        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            $merchantsBackendResources[] = $this->buildMerchantsBackendResource($merchantTransfer);
        }

        $paginationTransfer = $merchantCriteriaTransfer->getPaginationOrFail();
        $this->setCollectionPagination(
            $paginationTransfer->getOffsetOrFail(),
            $paginationTransfer->getLimitOrFail(),
            $paginationTransfer->getNbResults() ?? static::DEFAULT_COUNT,
        );

        return $merchantsBackendResources;
    }

    protected function buildMerchantsBackendResource(MerchantTransfer $merchantTransfer): MerchantsBackendResource
    {
        return $this->executeMerchantBackendResourceExpanderPlugins(
            $this->merchantResourceMapper->mapMerchantTransferToMerchantsBackendResource($merchantTransfer),
            $merchantTransfer,
        );
    }

    protected function executeMerchantBackendResourceExpanderPlugins(
        MerchantsBackendResource $merchantsBackendResource,
        MerchantTransfer $merchantTransfer,
    ): MerchantsBackendResource {
        foreach ($this->merchantBackendResourceExpanderPlugins as $merchantBackendResourceExpanderPlugin) {
            $merchantsBackendResource = $merchantBackendResourceExpanderPlugin->expand(
                $merchantsBackendResource,
                $merchantTransfer,
            );
        }

        return $merchantsBackendResource;
    }

    protected function buildMerchantCriteriaTransfer(): MerchantCriteriaTransfer
    {
        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setPagination($this->buildPaginationTransfer())
            ->setFilter(
                $this->collectionQueryReader->getFilterTransfer(
                    $this->getRequest(),
                    static::SORT_FIELD_MAP,
                ),
            );

        return $this->merchantCriteriaFilterReader->applyFiltersFromRequest($this->getRequest(), $merchantCriteriaTransfer);
    }
}
