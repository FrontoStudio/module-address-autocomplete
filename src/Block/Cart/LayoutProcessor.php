<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Block\Cart;

use Magento\Checkout\Block\Checkout\AttributeMerger;

class LayoutProcessor
{
    /**
     * @var AttributeMerger
     */
    protected $merger;
    
    public function __construct(AttributeMerger $merger) {
        $this->merger = $merger;
    }
    /**
     * Checkout LayoutProcessor after process plugin.
     *
     * @param \Magento\Checkout\Block\Cart\LayoutProcessor $processor
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(\Magento\Checkout\Block\Cart\LayoutProcessor $processor, $jsLayout)
    {
        $shippingConfiguration = &$jsLayout['components']['block-summary']['children']['block-shipping']['children']['address-fieldsets']
        ['children'];
        
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
        $elements = [
                'city' => [
                        'visible' => true,
                        'formElement' => 'input',
                        'label' => __('City'),
                        'value' =>  null,
                        'sortOrder' => '120'
                ],
                'street'=> [
                        'visible' => true,
                        'formElement' => 'multiline',
                        'label' => __('Street Address'),
                        'value' =>  null,
                        'sortOrder' => '110',
                        'size' => 1,
                        'required' => false,
                        'validation' => [
                        ],
                ],
            ];
        
        $addressFieldset['country_id']['visible'] = false;
        
        $addressFieldset = $this->merger->merge($elements, 'checkoutProvider', 'shippingAddress', $addressFieldset);

        if (isset($addressFieldset['street']['children'])) {
            $street0 = $addressFieldset['street']['children'][0];
            $street0['component'] = "FrontoStudio_AddressAutocomplete/js/form/element/autocomplete";
            $street0['config']['elementTmpl'] = "FrontoStudio_AddressAutocomplete/form/element/autocomplete";
            $addressFieldset['street']['children'][0] = $street0;
        }

        $addressFieldset['region_id']['sortOrder'] = '130';
        $addressFieldset['postcode']['sortOrder'] = '140';
        

        return $addressFieldset;
    }
}