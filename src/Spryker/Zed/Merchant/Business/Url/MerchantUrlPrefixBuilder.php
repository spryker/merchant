<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Merchant\Business\Url;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Merchant\MerchantConfig;

class MerchantUrlPrefixBuilder implements MerchantUrlPrefixBuilderInterface
{
    public function __construct(protected MerchantConfig $merchantConfig)
    {
    }

    public function buildLocalizedUrlPrefix(LocaleTransfer $localeTransfer): string
    {
        return sprintf('/%s/%s/', $this->extractLanguageCode($localeTransfer), $this->merchantConfig->getMerchantUrlPrefix());
    }

    protected function extractLanguageCode(LocaleTransfer $localeTransfer): string
    {
        $localeNameParts = explode('_', $localeTransfer->getLocaleNameOrFail());

        return $localeNameParts[0];
    }
}
