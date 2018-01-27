<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

use FrontoStudio\AddressAutocomplete\Helper\Data as DataHelper;

class Script 
    extends Template
    implements BlockInterface 
{
    protected $dataHelper;
    
    public function __construct(DataHelper $dataHelper, Template\Context $context, array $data = [])
    {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
    
    /**
     * Is script enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->dataHelper->isEnabled();
    }
    
    /**
     * Get Google API  key
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->dataHelper->getGoogleApiKey();
    }
}