<?php
class Efulfilment_BizzyBee_Helper_Order extends Mage_Core_Helper_Abstract
{
    private $aAddressFields = [
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'street' => 'street',
        // 'housenumber-addon' => 'address_2',
        'city' => 'city',
        'zip' => 'postcode',
        'country' => 'country_id',
        'phone_no' => 'telephone',
        'company' => 'company'
    ];

    private $aPaymentMethods = [
        // Add your payment methods here. E.g.: iDEAL => IDEAL 
        // See documentation for how to get the payment methods supported or contact us.
        'cashondelivery' => 'CASHDELIVERY',
    ];

    public function bb_doAction($data = null, $baseData) {
        
        $order = Mage::getModel('sales/order')->load($data);
   
        if( $order->getBaseTotalDue() == 0 ) {

            

            $aProductData = $this->getProducts($order);
            if(!empty($order->getCustomerId())){
                $customerData = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $aCustomerData = $this->getCustomer($customerData);
            }else{
                $aCustomerData = $this->getCustomer2($order);
            }
         
        
            //$baseData['order'] = $data;
            $baseData['order'] = $order->getIncrementId();
            
            $baseData['paid'] = $order->getGrandTotal();
            $baseData['payment_method'] = $this->aPaymentMethods[$order->getPayment()->getMethodInstance()->getCode()];
            
            $baseData['payment_method'] = 'IDEAL';
            $baseData['shipping_method'] = 'B2CTTD';
            $baseData['parcel_type'] = 'PARCELPLUS';
            $baseData['shop_type'] = 'Magento1';

            $baseData['items'] = $aProductData;
            $baseData['customer'] = $aCustomerData;

            $sData = json_encode($baseData, JSON_PRETTY_PRINT);

            $post = [
                'data' => $sData
            ];
        
            return  Mage::helper('BizzyBee/Base')->Execute('Order/add', $post);
        }
    }

    private function getProducts($order) {
        $aProducts = [];

        foreach($order->getAllVisibleItems() as $item) {
        
            $oItem = new \stdClass();

            $oItem->name = $item->getName();
            $oItem->price = $item->getPrice();
            $oItem->sku =  $item->getSku();
            $oItem->quantity = $item->getQtyOrdered();
            
            if(!empty($item->getDescription())) {
                $oItem->description = $item->getDescription();
            } else {
                $oItem->description = '';
            }

            $aProducts[] = $oItem;

        }

        return $aProducts;
    }

    private function getCustomer($customer) {

        $oUser = new stdClass();

        if(!empty($customer)) {
            $oUser->id = $customer->getID();
            $oUser->username = explode("@", $customer->getEmail(), 2)[0];
            $oUser->email = $customer->getEmail();
            $oUser->phone_no = $customer->getPrimaryShippingAddress()->getTelephone();
            $oUser->billing = $this->buildAddress($customer->getPrimaryBillingAddress());
            $oUser->shipping = $this->buildAddress($customer->getPrimaryShippingAddress());
        }

        return $oUser;
    }
    
    private function getCustomer2($order) {

        $oUser = new stdClass();

        if(!empty($order)) {
            $oUser->id = $order->getCustomerEmail();
            $oUser->username =  $order->getCustomerName();
            $oUser->email = $order->getCustomerEmail();
            $oUser->phone_no = $order->getBillingAddress()->getTelephone();
            $oUser->billing = $this->buildAddress($order->getBillingAddress());
            $oUser->shipping = $this->buildAddress($order->getShippingAddress());
        }

        return $oUser;
    }

    private function buildAddress($address) {
        $oAddress = new stdClass();

        foreach($this->aAddressFields as $key => $field) {
            if($key == 'street') {
                $street = $this->parseStreet($address[$field]);
                $oAddress->street = $street['street'];
                $oAddress->housenumber = $street['number'];
                $oAddress->{'housenumber-addon'} = $street['addon'];
            } else {
                $oAddress->{$key} = $address[$field];
            }
        }
        return $oAddress;

    }

    public function parseStreet($street, $streetIsSplitted = false)
    {
    	if($streetIsSplitted && is_array($street))
		{
			return array(
                'street' => isset($street[0]) ? $street[0] : null,
                'number' => isset($street[1]) ? $street[1] : null,
                'addon'  => isset($street[2]) ? $street[2] : null,
            );			
		}
		
		
        // Convert the street to a string
        if(is_array($street)) {
            $street = implode(' ', $street);
        }

        // Get some statistics on this street
        $parts = explode(' ', trim($street));
        $count = count($parts);

        // If there's only one segment
        if($count == 1) {
            return array(
                'street' => $street,
                'number' => null,
                'addon' => null,
            );
        }

        // If the last segment is numeric, assume it is the address
        if(is_numeric($parts[$count-1])) {
            $number = array_pop($parts);
            return array(
                'street' => implode(' ', $parts),
                'number' => $number,
                'addon' => null,
            );
        }

        // If the pre-last segment is numeric and the last segment is kind of short
        if($count > 2 && strlen($parts[$count-1]) < 5 && is_numeric($parts[$count-2])) {
            $addon = array_pop($parts);
            $number = array_pop($parts);
            return array(
                'street' => implode(' ', $parts),
                'number' => $number,
                'addon' => $addon,
            );
        }

        // If the last segment is pretty short
        if(strlen($parts[$count-1]) < 5) {
            $number = array_pop($parts);
            $addon = null;
            if(preg_match('/^([0-9]+)([^0-9]+)$/', $number, $matches)) {
                $number = $matches[1];
                $addon = $matches[2];
            }
            return array(
                'street' => implode(' ', $parts),
                'number' => $number,
                'addon' => $addon,
            );
        }

        // Last resort: Just use the last segment as number
        $number = array_pop($parts);
        return array(
            'street' => implode(' ', $parts),
            'number' => $number,
            'addon' => null,
        );
    }
}