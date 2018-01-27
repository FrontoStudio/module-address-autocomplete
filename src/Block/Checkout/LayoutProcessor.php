<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Block\Checkout;

class LayoutProcessor
{
    /**
     * Checkout LayoutProcessor after process plugin.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $shippingConfiguration = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
        
        if (isset($shippingConfiguration)) {
            $shippingConfiguration = $this->_processAddress(
                $shippingConfiguration
            );
        }
        return $jsLayout;
    }

    /**
     * Process provided address afterRender fields.
     *
     * @param $addressFieldset - Address fieldset config.
     * @return array
     */
    private function _processAddress($addressFieldset)
    {
        //Makes each address field label trackable.
        if (isset($addressFieldset['street']['children'])) {
            $street0 = $addressFieldset['street']['children'][0];
            $street0['component'] = "FrontoStudio_AddressAutocomplete/js/form/element/autocomplete";
            $street0['config']['elementTmpl'] = "FrontoStudio_AddressAutocomplete/form/element/autocomplete";
            $addressFieldset['street']['children'][0] = $street0;
        }
        
        //addcustom fields
        $latitudeAttributeCode = 'fs_latitude';
        $latitude = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                        'customScope' => 'shippingAddress.custom_attributes',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/hidden',
                        'id' => $latitudeAttributeCode
                ],
                'dataScope' => 'shippingAddress.custom_attributes.' . $latitudeAttributeCode,
                'label' => 'Latitude',
                'provider' => 'checkoutProvider',
                'visible' => false,
                'validation' => [
                ],
                'sortOrder' => 0,
                'id' => $latitudeAttributeCode
            ];
        $longitudeAttributeCode = 'fs_longitude';
        $longitude = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                        'customScope' => 'shippingAddress.custom_attributes',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/hidden',
                        'id' => $longitudeAttributeCode
                ],
                'dataScope' => 'shippingAddress.custom_attributes.' . $longitudeAttributeCode,
                'label' => 'Longitude',
                'provider' => 'checkoutProvider',
                'visible' => false,
                'validation' => [
                ],
                'sortOrder' => 0,
                'id' => $longitudeAttributeCode
        ];
        
        $addressFieldset[$latitudeAttributeCode] = $latitude;
        $addressFieldset[$longitudeAttributeCode] = $longitude;

        return $addressFieldset;
    }
}