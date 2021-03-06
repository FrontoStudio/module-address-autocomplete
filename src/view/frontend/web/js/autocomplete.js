/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/model/customer',
], function (
    $,
    uiRegistry,
    quote,
    addressConverter,
    shippingService,
    rateRegistry,
    customerAddressProcessor,
    newAddressProcessor,
    selectShippingAddress,
    setShippingInformationAction,
    checkoutData,
    customer
) {
    'use strict';

    var componentForm = {
        subpremise: 'short_name',
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'short_name',
        postal_code: 'short_name',
        postal_town: 'short_name',
        sublocality_level_1: 'short_name'
    };

    var lookupElement = {
        street_number: 'street_1',
        route: 'street_2',
        locality: 'city',
        administrative_area_level_1: 'region',
        country: 'country_id',
        postal_code: 'postcode'
    };
    
    var autocomplete;

    var initAutocomplete = function () {
        var loaded = false;
        uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0', function(streetEl) {
            if (loaded == false) {
                var geocoder = new google.maps.Geocoder();

                var domID = streetEl.uid;

                var street = $('#'+domID);
                street.each(function () {
                var element = this;
                autocomplete = new google.maps.places.Autocomplete(
                    /** @type {!HTMLInputElement} */(this),
                    {types: ['geocode']}
                );
                autocomplete.addListener('place_changed', fillInAddress);
            });
            $('#'+domID).focus(geolocate);
            loaded = true;
        }
    });
    },
    fillInAddress = function () {
        var place = autocomplete.getPlace();

        var street = [];
        var region  = '';
        var streetNumber = '';
        var city = '';

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var value = place.address_components[i][componentForm[addressType]];
                if (addressType == 'subpremise') {
                    streetNumber = value + '/';
                } else if (addressType == 'street_number') {
                    streetNumber = streetNumber + value;
                } else if (addressType == 'route') {
                    street[1] = value;
                } else if (addressType == 'administrative_area_level_1') {
                    region = value;
                } else if (addressType == 'sublocality_level_1') {
                    city = value;
                } else if (addressType == 'postal_town') {
                    city = value;
                } else if (addressType == 'locality' && city == '') {
                    //ignore if we are using one of other city values already
                    city = value;
                } else {
                    var elementId = lookupElement[addressType];
                    var thisDomID = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.'+ elementId).uid;
                    if ($('#'+thisDomID)) {
                        $('#'+thisDomID).val(value);
                        $('#'+thisDomID).trigger('change');
                    }
                }
            }
        }
        if (street.length > 0) {
            street[0] = streetNumber;
            var domID = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street').elems()[0].uid;
            var streetString = street.join(' ');
            if ($('#'+domID)) {
                $('#'+domID).val(streetString);
                $('#'+domID).trigger('change');
            }
            if (uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.fs_latitude')) {
                var latitudeDomId = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.fs_latitude').uid;
                if ($('#'+latitudeDomId)) {
                    $('#'+latitudeDomId).val(place.geometry.location.lat());
                    $('#'+latitudeDomId).trigger('change');
                }
            }
            if (uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.fs_longitude')) {
                var longitudeDomId = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.fs_longitude').uid;
                if ($('#'+longitudeDomId)) {
                    $('#'+longitudeDomId).val(place.geometry.location.lng());
                    $('#'+longitudeDomId).trigger('change');
                }
            }
        }
        var cityDomID = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city').uid;
        if ($('#'+cityDomID)) {
            $('#'+cityDomID).val(city);
            $('#'+cityDomID).trigger('change');
        }
        if (region != '') {
            if (uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id')) {
                var regionDomId = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id').uid;
                if ($('#'+regionDomId)) {
                    //search for and select region using text
                    $('#'+regionDomId +' option')
                        .filter(function () {
return $.trim($(this).text()) == region; })
                        .attr('selected',true);
                    $('#'+regionDomId).trigger('change');
                }
            }
            if (uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id_input')) {
                var regionDomId = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id_input').uid;
                if ($('#'+regionDomId)) {
                    $('#'+regionDomId).val(region);
                    $('#'+regionDomId).trigger('change');
                }
            }
        }
        var shippingAddress = quote.shippingAddress();
     // clearing cached rates to retrieve new ones
        rateRegistry.set(shippingAddress.getKey(), null);
        rateRegistry.set(shippingAddress.getCacheKey(), null);
        var addressData = addressConverter.formAddressDataToQuoteAddress(
                checkoutData.getShippingAddressFromData()
            );

        //Copy form data to quote shipping address object
        for (var field in addressData) {
            if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                shippingAddress.hasOwnProperty(field) &&
                typeof addressData[field] != 'function' &&
                _.isEqual(shippingAddress[field], addressData[field])
            ) {
                shippingAddress[field] = addressData[field];
            } else if (typeof addressData[field] != 'function' &&
                !_.isEqual(shippingAddress[field], addressData[field])) {
                shippingAddress = addressData;
                break;
            }
        }

        if (customer.isLoggedIn()) {
            shippingAddress['save_in_address_book'] = 1;
        }
        selectShippingAddress(shippingAddress);
    },
    geolocate = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }
    
    return initAutocomplete;
});