<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Plugin\Quote\Model;

class ShippingAddressManagement
{
    protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    
    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            if (!$extAttributes->getCheckoutFields()) {
                $extAttributes->setCheckoutFields(array());
            }
            try {
                $address->setFsLatitude($extAttributes->getFsLatitude());
                $address->setFsLongitude($extAttributes->getFsLongitude());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}