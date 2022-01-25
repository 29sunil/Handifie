<?php
require_once "Mage/Adminhtml/controllers/Sales/OrderController.php";
class Efulfilment_BizzyBee_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController{

   

    public function cancelAction()
    {
        
		
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()->save();


	            $baseData['order_id'] = $order->getId();
	            $baseData['order_status'] = "cancelled";

	            $sData = json_encode($baseData, JSON_PRETTY_PRINT);

	            $post = [
	                'data' => $sData
	            ];
	        
            	Mage::helper('BizzyBee/Base')->Execute('Order/updatestatus', $post);

                $this->_getSession()->addSuccess(
                    $this->__('The order has been cancelled.')
                );

                
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }


}
				