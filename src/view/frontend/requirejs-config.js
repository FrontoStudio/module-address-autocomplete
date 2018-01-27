/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'FrontoStudio_AddressAutocomplete/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-method': {
                'FrontoStudio_AddressAutocomplete/js/action/select-shipping-method-mixin': true
            }
        }
    }
};