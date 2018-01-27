/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-shipping-information'
], function (wrapper, quote, setShippingInfoAction) {
    'use strict';

    return function (selectShippingMethodAction) {

        return wrapper.wrap(selectShippingMethodAction, function (originalAction, shippingMethod) {
            var action = originalAction(shippingMethod);
            if (quote.shippingMethod()) {
                // Update totals on summary
                setShippingInfoAction([]);
            }
            return action;
        });
    };
});