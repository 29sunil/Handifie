<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import entity product model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Efulfilment_BizzyBee_Model_Import_Entity_Product extends Mage_ImportExport_Model_Import_Entity_Product
{
    // protected function _saveProductEntity(array $entityRowsIn, array $entityRowsUp)
    // {
    //     parent::_saveProductEntity($entityRowsIn, $entityRowsUp);
    //     //$base = Mage::helper('BizzyBee/Base')->getBase();
        
    // }
    protected function _saveProductAttributes(array $attributesData)
    {
    	parent::_saveProductAttributes($attributesData);
    	$base = Mage::helper('BizzyBee/Base')->getBase();
        $productArray = $this->_newSku;
        foreach ($productArray as $value) {
        	//echo "<pre>";
        	//print_r($value['entity_id']);
        	$data = $value['entity_id'];
        	/*$product = Mage::getModel('catalog/product')->load($data);
        	echo "<br>dsf";echo $sSku = $product->getSku();
        	echo "<br>getPrice";echo $sSku = $product->getPrice();
        	echo "<br>getName";echo $sSku = $product->getName();*/
        	$result = Mage::helper('BizzyBee/Product')->bb_doAction($data, $base);
        }
       // exit();
    }
}
