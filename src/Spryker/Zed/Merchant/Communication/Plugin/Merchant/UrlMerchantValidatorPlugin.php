<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantValidatorPluginInterface;

/**
 * @deprecated Will be removed without replacement. The validation is applied by {@link \Spryker\Zed\Merchant\Business\MerchantFacadeInterface::createMerchant()} and {@link \Spryker\Zed\Merchant\Business\MerchantFacadeInterface::updateMerchant()} out of the box. Remove the plugin from {@link \Spryker\Zed\Merchant\MerchantDependencyProvider::getMerchantValidatorPlugins()}, otherwise the violation is reported twice.
 *
 * @method \Spryker\Zed\Merchant\Business\MerchantBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\Merchant\Business\MerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\Merchant\Communication\MerchantCommunicationFactory getFactory()
 * @method \Spryker\Zed\Merchant\MerchantConfig getConfig()
 */
class UrlMerchantValidatorPlugin extends AbstractPlugin implements MerchantValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Validates that every `MerchantTransfer.urlCollection` entry is present, short enough for `spy_url.url` and not owned by another resource.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        return $this->getBusinessFactory()
            ->createUrlMerchantValidator()
            ->validate($merchantTransfer);
    }
}
