<?php

class Efulfilment_BizzyBee_Model_Observer
{
    private $aTypes = [
        'customer',
        'product',
        'order'
    ];
    // Magento passes a Varien_Event_Observer object as the first parameter of dispatched events.
    public function syncProduct(Varien_Event_Observer $observer)
    {
        // Retrieve the product being updated from the event observer
        $product = $observer->getEvent()->getProduct();
        $id = $product->getId();

        $this->doRequest('product', $id);
    }

    public function syncCustomer(Varien_Event_Observer $observer)
    {
        // Retrieve the product being updated from the event observer
        $customer = $observer->getEvent()->getCustomer();
        $id = $customer->getId();

        $this->doRequest('customer', $id);
    }
    public function syncOrder(Varien_Event_Observer $observer)
    {
        // // Retrieve the product being updated from the event observer
        $order = $observer->getEvent()->getOrder();
        $id = $order->getId();

        $this->doRequest('order', $id);
    }

    private function doRequest($sType, $data = null) {
        
        if(in_array($sType, $this->aTypes)) {
            $base = Mage::helper('BizzyBee/Base')->getBase();
           
            if ($sType == 'product') {
                $result = Mage::helper('BizzyBee/Product')->bb_doAction($data, $base);
            } else if ($sType == 'customer') {
                $result = Mage::helper('BizzyBee/Customer')->bb_doAction($data, $base);
            } else if ($sType == 'order') {
                $result = Mage::helper('BizzyBee/Order')->bb_doAction($data, $base);
            }
            
           
            if(isset($result->sonce)) {
                Mage::getConfig()->saveConfig('module/bizzybee/sonse', $result->sonce, 'default', 0);
            }

            return $result;

        } else {
            return ['error' => true, 'message' => 'invalid type'];
        }
    }
}
?>