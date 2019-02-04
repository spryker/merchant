<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Model\Status;

use Spryker\Zed\Merchant\MerchantConfig;

class MerchantStatusReader implements MerchantStatusReaderInterface
{
    /**
     * @var \Spryker\Zed\Merchant\MerchantConfig
     */
    protected $config;

    /**
     * @param \Spryker\Zed\Merchant\MerchantConfig $config
     */
    public function __construct(MerchantConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $currentStatus
     *
     * @return string[]
     */
    public function getNextStatuses(string $currentStatus): array
    {
        $statusTree = $this->config->getStatusTree();

        return $statusTree[$currentStatus] ?? [];
    }
}