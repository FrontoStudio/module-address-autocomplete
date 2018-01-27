<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\Url\Helper\Data
{
    const XML_PATH_AUTOCOMPLETE_ENABLE = 'shipping/fronto_autocomplete/enable';
    
    const XML_PATH_GOOGLE_API_KEY = 'shipping/fronto_autocomplete/google_api_key';
    
    const XML_PATH_GOOGLE_LAT = 'shipping/origin/latitude';
    
    const XML_PATH_GOOGLE_LONG = 'shipping/origin/longitude';
    
    /**
     * Is script enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->isSetFlag(
                self::XML_PATH_AUTOCOMPLETE_ENABLE,
                ScopeInterface::SCOPE_STORE
                );
    }
    
    /**
     * Get Google API  key
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLE_API_KEY,
                ScopeInterface::SCOPE_STORE
                );
    }
    
    public function getOriginLatitude()
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLE_LAT,
                ScopeInterface::SCOPE_WEBSITE
                );
    }
    
    public function getOriginLongitude()
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLE_LONG,
                ScopeInterface::SCOPE_WEBSITE
                );
    }
}