<?php
/**
 * Copyright © Fronto Studio. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FrontoStudio\AddressAutocomplete\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        
        /**
         * @var \Magento\Eav\Setup\EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
                'customer_address',
                'fs_latitude',
                [
                    'type'         => 'varchar',
                    'label'        => 'Google Latitude',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => true,
                    'position'     => 888,
                    'system'       => 0,
                ]
            )->addAttribute(
                'customer_address',
                'fs_longitude',
                [
                    'type'         => 'varchar',
                    'label'        => 'Google Longitude',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => true,
                    'position'     => 999,
                    'system'       => 0,
                ]
        );
    }
}