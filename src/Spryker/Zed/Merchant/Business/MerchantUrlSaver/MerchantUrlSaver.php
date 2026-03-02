<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\MerchantUrlSaver;

use ArrayObject;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToUrlFacadeInterface;

class MerchantUrlSaver implements MerchantUrlSaverInterface
{
    /**
     * @var \Spryker\Zed\Merchant\Dependency\Facade\MerchantToUrlFacadeInterface
     */
    protected $urlFacade;

    public function __construct(MerchantToUrlFacadeInterface $urlFacade)
    {
        $this->urlFacade = $urlFacade;
    }

    public function saveMerchantUrls(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $urlTransferCollection = new ArrayObject();
        foreach ($merchantTransfer->getUrlCollection() as $merchantUrlTransfer) {
            $urlTransfer = new UrlTransfer();
            $urlTransfer->fromArray($merchantUrlTransfer->modifiedToArray(), false);
            $urlTransfer->setFkResourceMerchant($merchantTransfer->getIdMerchant());

            $urlTransfer = $this->saveMerchantUrl($urlTransfer);
            $urlTransferCollection->append($urlTransfer);
        }

        $merchantTransfer->setUrlCollection($urlTransferCollection);

        return $merchantTransfer;
    }

    protected function saveMerchantUrl(UrlTransfer $urlTransfer): UrlTransfer
    {
        if ($urlTransfer->getIdUrl() === null) {
            return $this->urlFacade->createUrl($urlTransfer);
        }

        return $this->urlFacade->updateUrl($urlTransfer);
    }
}
