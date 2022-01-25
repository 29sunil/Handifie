<?php
class Efulfilment_BizzyBee_SyncbizzbeeController extends Mage_Core_Controller_Front_Action
{  
    public function indexAction()
    {

  //   echo "<pre>";
  //  print_r($_POST); die;
//echo "Nisha";exit();
    $orderId = $_POST['shop_order'];
    
    $order = Mage::getModel('sales/order')->load($orderId);
    if(empty($order->getData())){
         $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
         $orderId =  $order->getId(); 
    }
    
    if ($_POST['order_status'] == 'Processing' || $_POST['order_status'] == 'Packing' || $_POST['order_status']=='Working on') {
      $order->setData('state', Mage_Sales_Model_Order::STATE_PROCESSING); 
     $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING); 
     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
     $order->save(); 
    
    }
    
    
    if ($_POST['order_status']=='Shipped') {
                //create shipment
                $itemQty =  $order->getItemsCollection()->count();
                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
                $shipment = new Mage_Sales_Model_Order_Shipment_Api();
                $shipmentId = $shipment->create( $order->getIncrementId(), array(), 'Shipment created through ShipMailInvoice', true, true);
                //add tracking info
                $shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
                
                
                $shipment_collection->addAttributeToFilter('order_id', $orderId);
                
             
                
                
                foreach($shipment_collection as $sc)
                {
                $shipment = Mage::getModel('sales/order_shipment');
                $shipment->load($sc->getId());
                        if($shipment->getId() != '')
                        {
                            try
                            {
                                 Mage::getModel('sales/order_shipment_track')
                                 ->setShipment($shipment)
                                 ->setData('title', $_POST['track_method'])
                                 ->setData('number', $_POST['track_code'])
                                 ->setData('carrier_code', $_POST['track_code'])
                                 ->setData('order_id', $shipment->getData('order_id'))
                                 ->save();
                                //die('Plese ');
                            }catch (Exception $e)
                            {
                                Mage::getSingleton('core/session')->addError('order id '.$orderId.' no found');
                            }
                        }
                }
     
                //$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE); 
                //$order->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE); 
                //$history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
                //$order->save(); 
    
    }
    
    
    if ($_POST['order_status'] == 'Awaiting payment') {
      $order->setData('state', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT); 
     $order->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT); 
     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
     $order->save(); 
    
    }
   
    
    if ($_POST['order_status'] == 'Cancelled') {
      $order->setData('state', Mage_Sales_Model_Order::STATE_CANCELED); 
     $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED); 
     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
     $order->save(); 
    
    }

    if ($_POST['order_status'] == 'On Hold') {
      $order->setData('state', Mage_Sales_Model_Order::STATE_HOLDED); 
      $order->setStatus(Mage_Sales_Model_Order::STATE_HOLDED); 
      $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
      $order->save(); 
    }
    echo "Done";
     /*$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE); 
     $order->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE); 
     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false); 
     $order->save(); */
    
   
    }
}
