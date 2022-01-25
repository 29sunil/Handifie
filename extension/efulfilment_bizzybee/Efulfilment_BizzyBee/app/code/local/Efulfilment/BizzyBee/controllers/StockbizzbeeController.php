<?php
class Efulfilment_BizzyBee_StockbizzbeeController extends Mage_Core_Controller_Front_Action
{  
    public function indexAction()
    {

        if(!empty($_POST)) { 
            // Load your product object
            $sku = $_POST['productsku'];
            $qty = $_POST['avi_stock'];
    
    
            $_catalog = Mage::getModel('catalog/product');
            $_productId = $_catalog->getIdBySku($sku);
            $_product = Mage::getModel('catalog/product')->load($_productId);
             
            // If product exists, get the inventory items information
            if ($_product) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
                  
                $stockItem->setQty($qty);
                $stockItem->setIsInStock((bool)$qty);
                $stockItem->save();
                echo "Done";
            }
        }
	}
}
