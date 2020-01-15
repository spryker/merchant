<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Merchant\Business\Model\MerchantCreator;
use Spryker\Zed\Merchant\Business\Model\MerchantCreatorInterface;
use Spryker\Zed\Merchant\Business\Model\MerchantReader;
use Spryker\Zed\Merchant\Business\Model\MerchantReaderInterface;
use Spryker\Zed\Merchant\Business\Model\MerchantUpdater;
use Spryker\Zed\Merchant\Business\Model\MerchantUpdaterInterface;
use Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusReader;
use Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusReaderInterface;
use Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidator;
use Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidatorInterface;
use Spryker\Zed\Merchant\Dependency\Service\MerchantToUtilTextServiceInterface;
use Spryker\Zed\Merchant\MerchantDependencyProvider;

/**
 * @method \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\Merchant\Persistence\MerchantEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\Merchant\MerchantConfig getConfig()
 */
class MerchantBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Merchant\Business\Model\MerchantCreatorInterface
     */
    public function createMerchantCreator(): MerchantCreatorInterface
    {
        return new MerchantCreator(
            $this->getEntityManager(),
            $this->getConfig(),
            $this->getMerchantPostCreatePlugins()
        );
    }

    /**
     * @return \Spryker\Zed\Merchant\Business\Model\MerchantUpdaterInterface
     */
    public function createMerchantUpdater(): MerchantUpdaterInterface
    {
        return new MerchantUpdater(
            $this->getEntityManager(),
            $this->getRepository(),
            $this->createMerchantStatusValidator(),
            $this->getMerchantPostUpdatePlugins()
        );
    }

    /**
     * @return \Spryker\Zed\Merchant\Business\Model\MerchantReaderInterface
     */
    public function createMerchantReader(): MerchantReaderInterface
    {
        return new MerchantReader(
            $this->getRepository(),
            $this->getMerchantExpanderPlugins()
        );
    }

    /**
     * @return \Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusReaderInterface
     */
    public function createMerchantStatusReader(): MerchantStatusReaderInterface
    {
        return new MerchantStatusReader(
            $this->getConfig()
        );
    }

    /**
     * @return \Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidatorInterface
     */
    public function createMerchantStatusValidator(): MerchantStatusValidatorInterface
    {
        return new MerchantStatusValidator(
            $this->createMerchantStatusReader()
        );
    }

    /**
     * @return \Spryker\Zed\Merchant\Dependency\Service\MerchantToUtilTextServiceInterface
     */
    public function getUtilTextService(): MerchantToUtilTextServiceInterface
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::SERVICE_UTIL_TEXT);
    }

    /**
     * @deprecated Use \Spryker\Zed\Merchant\Business\MerchantBusinessFactory::getMerchantPostCreatePlugins() instead.
     *
     * @return \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantPostSavePluginInterface[]
     */
    public function getMerchantPostSavePlugins(): array
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_POST_SAVE);
    }

    /**
     * @return \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantPostCreatePluginInterface[]
     */
    public function getMerchantPostCreatePlugins(): array
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_POST_CREATE);
    }

    /**
     * @return \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantPostUpdatePluginInterface[]
     */
    public function getMerchantPostUpdatePlugins(): array
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_POST_UPDATE);
    }

    /**
     * @return \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantExpanderPluginInterface[]
     */
    public function getMerchantExpanderPlugins(): array
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_EXPANDER);
    }
}
