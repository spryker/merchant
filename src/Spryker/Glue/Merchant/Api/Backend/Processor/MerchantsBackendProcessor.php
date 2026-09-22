<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant\Api\Backend\Processor;

use ArrayObject;
use Generated\Api\Backend\MerchantsBackendResource;
use Generated\Shared\Transfer\LocaleConditionsTransfer;
use Generated\Shared\Transfer\LocaleCriteriaTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantProfileTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreConditionsTransfer;
use Generated\Shared\Transfer\StoreCriteriaTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractBackendProcessor;
use Spryker\Glue\Merchant\Api\Backend\Exception\MerchantBackendExceptionFactory;
use Spryker\Glue\Merchant\Api\Backend\Mapper\MerchantResourceMapperInterface;
use Spryker\Glue\Merchant\Api\Backend\Reader\MerchantReaderInterface;
use Spryker\Glue\Merchant\Api\Backend\Writer\MerchantWriterInterface;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;

class MerchantsBackendProcessor extends AbstractBackendProcessor
{
    protected const string URI_VARIABLE_MERCHANT_REFERENCE = 'merchantReference';

    protected const string RESOURCE_KEY_URL_LOCALE_NAME = 'localeName';

    protected const string RESOURCE_KEY_URL = 'url';

    /**
     * @param array<\Spryker\Glue\MerchantExtension\Dependency\Plugin\MerchantBackendResourceExpanderPluginInterface> $merchantBackendResourceExpanderPlugins
     * @param array<\Spryker\Glue\MerchantExtension\Dependency\Plugin\MerchantBackendTransferExpanderPluginInterface> $merchantBackendTransferExpanderPlugins
     */
    public function __construct(
        protected MerchantReaderInterface $merchantReader,
        protected MerchantWriterInterface $merchantWriter,
        protected MerchantBackendExceptionFactory $exceptionFactory,
        protected StoreFacadeInterface $storeFacade,
        protected LocaleFacadeInterface $localeFacade,
        protected MerchantFacadeInterface $merchantFacade,
        protected MerchantResourceMapperInterface $merchantResourceMapper,
        #[Plugins(dependencyProviderMethod: 'getMerchantBackendResourceExpanderPlugins')]
        protected array $merchantBackendResourceExpanderPlugins = [],
        #[Plugins(dependencyProviderMethod: 'getMerchantBackendTransferExpanderPlugins')]
        protected array $merchantBackendTransferExpanderPlugins = [],
    ) {
    }

    protected function processPost(mixed $data): object
    {
        $merchantTransfer = $this->applyCreateDefaults(
            $this->mapResourceDataToTransfer($data, new MerchantTransfer()),
        );

        $this->assertUrlProvidedForEveryMerchantStoreLocale($merchantTransfer);

        return $this->buildMerchantsBackendResource($this->merchantWriter->createMerchant($merchantTransfer));
    }

    protected function processPatch(mixed $data): object
    {
        $merchantTransfer = $this->merchantReader->getMerchantByReference(
            (string)$this->getUriVariable(static::URI_VARIABLE_MERCHANT_REFERENCE),
        );
        $merchantTransfer = $this->mapResourceDataToTransfer($data, $merchantTransfer);

        $this->assertUrlProvidedForEveryMerchantStoreLocale($merchantTransfer);

        return $this->buildMerchantsBackendResource($this->merchantWriter->updateMerchant($merchantTransfer));
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

    protected function applyCreateDefaults(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        if ($merchantTransfer->getStoreRelation() === null) {
            $merchantTransfer->setStoreRelation(new StoreRelationTransfer());
        }

        if ($merchantTransfer->getMerchantProfile() === null) {
            $merchantTransfer->setMerchantProfile(new MerchantProfileTransfer());
        }

        return $merchantTransfer;
    }

    protected function mapResourceDataToTransfer(mixed $data, MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        /** @var \Generated\Api\Backend\MerchantsBackendResource $merchantsBackendResource */
        $merchantsBackendResource = $data;

        if ($merchantTransfer->getIdMerchant() === null && $merchantsBackendResource->merchantReference !== null) {
            $merchantTransfer->setMerchantReference($merchantsBackendResource->merchantReference);
        }

        if ($merchantsBackendResource->name !== null) {
            $merchantTransfer->setName($merchantsBackendResource->name);
        }

        if ($merchantsBackendResource->email !== null) {
            $merchantTransfer->setEmail($merchantsBackendResource->email);
        }

        $merchantTransfer->setRegistrationNumber($merchantsBackendResource->registrationNumber);

        if ($merchantsBackendResource->status !== null) {
            $merchantTransfer->setStatus($merchantsBackendResource->status);
        }

        if ($merchantsBackendResource->isActive !== null) {
            $merchantTransfer->setIsActive($merchantsBackendResource->isActive);
        }

        if ($merchantsBackendResource->stores !== null) {
            $merchantTransfer->setStoreRelation($this->buildStoreRelationTransfer($merchantsBackendResource->stores));
        }

        if ($merchantsBackendResource->merchantUrls !== null) {
            $merchantTransfer->setUrlCollection(
                $this->buildUrlCollection($merchantsBackendResource->merchantUrls, $merchantTransfer),
            );
        }

        return $this->executeMerchantBackendTransferExpanderPlugins($merchantTransfer, $merchantsBackendResource);
    }

    protected function executeMerchantBackendTransferExpanderPlugins(
        MerchantTransfer $merchantTransfer,
        MerchantsBackendResource $merchantsBackendResource,
    ): MerchantTransfer {
        foreach ($this->merchantBackendTransferExpanderPlugins as $merchantBackendTransferExpanderPlugin) {
            $merchantTransfer = $merchantBackendTransferExpanderPlugin->expand(
                $merchantTransfer,
                $merchantsBackendResource,
            );
        }

        return $merchantTransfer;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a required locale has no URL.
     */
    protected function assertUrlProvidedForEveryMerchantStoreLocale(MerchantTransfer $merchantTransfer): void
    {
        $storeNames = $this->getMerchantStoreNames($merchantTransfer);

        if ($storeNames === []) {
            return;
        }

        $existingUrlTransfersByLocaleName = $this->getUrlTransfersIndexedByLocaleName($merchantTransfer);

        $localeCriteriaTransfer = (new LocaleCriteriaTransfer())->setLocaleConditions(
            (new LocaleConditionsTransfer())->setStoreNames($storeNames),
        );

        foreach ($this->localeFacade->getLocaleCollection($localeCriteriaTransfer) as $storeLocaleTransfer) {
            $localeName = $storeLocaleTransfer->getLocaleNameOrFail();

            if (!isset($existingUrlTransfersByLocaleName[$localeName])) {
                throw $this->exceptionFactory->createMissingMerchantUrlLocaleException($localeName);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function getMerchantStoreNames(MerchantTransfer $merchantTransfer): array
    {
        $idStores = $merchantTransfer->getStoreRelationOrFail()->getIdStores();

        if ($idStores === []) {
            return [];
        }

        $storeCriteriaTransfer = (new StoreCriteriaTransfer())->setStoreConditions(
            (new StoreConditionsTransfer())->setStoreIds($idStores),
        );

        $storeNames = [];

        foreach ($this->storeFacade->getStoreCollection($storeCriteriaTransfer)->getStores() as $storeTransfer) {
            $storeNames[] = $storeTransfer->getNameOrFail();
        }

        return $storeNames;
    }

    /**
     * @param array<int, mixed> $storeNames
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a store name does not exist.
     */
    protected function buildStoreRelationTransfer(array $storeNames): StoreRelationTransfer
    {
        $storeNames = array_values(array_map('strval', $storeNames));
        $storeRelationTransfer = new StoreRelationTransfer();

        if ($storeNames === []) {
            return $storeRelationTransfer;
        }

        $idStoresByStoreName = [];

        foreach ($this->storeFacade->getStoreTransfersByStoreNames($storeNames) as $storeTransfer) {
            $idStoresByStoreName[$storeTransfer->getNameOrFail()] = $storeTransfer->getIdStoreOrFail();
        }

        foreach ($storeNames as $storeName) {
            if (!isset($idStoresByStoreName[$storeName])) {
                throw $this->exceptionFactory->createUnknownStoreException($storeName);
            }

            $storeRelationTransfer->addIdStores($idStoresByStoreName[$storeName]);
        }

        return $storeRelationTransfer;
    }

    /**
     * Every locale absent from `$requestedMerchantUrls` keeps the URL it already has on
     * `$merchantTransfer` - the result is an upsert per locale, never a wholesale replacement.
     *
     * @param array<int, mixed> $requestedMerchantUrls
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException When a locale name does not exist.
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\UrlTransfer>
     */
    protected function buildUrlCollection(array $requestedMerchantUrls, MerchantTransfer $merchantTransfer): ArrayObject
    {
        $requestedMerchantUrlRows = array_map(static fn (mixed $requestedMerchantUrl): array => (array)$requestedMerchantUrl, $requestedMerchantUrls);
        $urlTransfersByLocaleName = $this->getUrlTransfersIndexedByLocaleName($merchantTransfer);
        $requestedLocaleTransfersByLocaleName = $this->getLocaleTransfersIndexedByLocaleName($requestedMerchantUrlRows);

        foreach ($requestedMerchantUrlRows as $requestedMerchantUrlRow) {
            $localeName = (string)($requestedMerchantUrlRow[static::RESOURCE_KEY_URL_LOCALE_NAME] ?? '');

            if (!isset($requestedLocaleTransfersByLocaleName[$localeName])) {
                throw $this->exceptionFactory->createUnknownLocaleException($localeName);
            }

            $localeTransfer = $requestedLocaleTransfersByLocaleName[$localeName];

            $urlTransfer = $urlTransfersByLocaleName[$localeName] ?? (new UrlTransfer())
                ->setLocaleName($localeName)
                ->setFkLocale($localeTransfer->getIdLocaleOrFail());

            $requestedUrl = (string)($requestedMerchantUrlRow[static::RESOURCE_KEY_URL] ?? '');
            $urlTransfersByLocaleName[$localeName] = $urlTransfer->setUrl(
                $this->applyMerchantUrlPrefix($requestedUrl, $localeTransfer),
            );
        }

        return new ArrayObject(array_values($urlTransfersByLocaleName));
    }

    protected function applyMerchantUrlPrefix(string $url, LocaleTransfer $localeTransfer): string
    {
        if ($url === '') {
            return $url;
        }

        $urlPrefix = $this->merchantFacade->buildMerchantUrlPrefixForLocale($localeTransfer);

        if ($this->hasMerchantUrlPrefix($url, $urlPrefix)) {
            return $url;
        }

        return $urlPrefix . ltrim($url, '/');
    }

    protected function hasMerchantUrlPrefix(string $url, string $urlPrefix): bool
    {
        return stripos($url, $urlPrefix) === 0;
    }

    /**
     * @param array<int, array<string, mixed>> $requestedMerchantUrlRows
     *
     * @return array<string, \Generated\Shared\Transfer\LocaleTransfer>
     */
    protected function getLocaleTransfersIndexedByLocaleName(array $requestedMerchantUrlRows): array
    {
        $localeNames = [];

        foreach ($requestedMerchantUrlRows as $requestedMerchantUrlRow) {
            $localeName = (string)($requestedMerchantUrlRow[static::RESOURCE_KEY_URL_LOCALE_NAME] ?? '');

            if ($localeName !== '') {
                $localeNames[] = $localeName;
            }
        }

        $localeNames = array_values(array_unique($localeNames));

        if ($localeNames === []) {
            return [];
        }

        return $this->localeFacade->getLocaleCollection(
            (new LocaleCriteriaTransfer())->setLocaleConditions(
                (new LocaleConditionsTransfer())->setLocaleNames($localeNames),
            ),
        );
    }

    /**
     * @return array<string, \Generated\Shared\Transfer\UrlTransfer>
     */
    protected function getUrlTransfersIndexedByLocaleName(MerchantTransfer $merchantTransfer): array
    {
        $urlTransfersByLocaleName = [];

        foreach ($merchantTransfer->getUrlCollection() as $urlTransfer) {
            $urlTransfersByLocaleName[$urlTransfer->getLocaleNameOrFail()] = $urlTransfer;
        }

        return $urlTransfersByLocaleName;
    }
}
