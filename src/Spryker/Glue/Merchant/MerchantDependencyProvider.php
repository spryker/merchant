<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Merchant;

use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;

/**
 * @method \Spryker\Glue\Merchant\MerchantConfig getConfig()
 */
class MerchantDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @return array<\Spryker\Glue\MerchantExtension\Dependency\Plugin\MerchantBackendResourceExpanderPluginInterface>
     */
    protected function getMerchantBackendResourceExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Glue\MerchantExtension\Dependency\Plugin\MerchantBackendTransferExpanderPluginInterface>
     */
    protected function getMerchantBackendTransferExpanderPlugins(): array
    {
        return [];
    }
}
