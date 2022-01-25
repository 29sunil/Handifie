<?php
class Efulfilment_BizzyBee_Helper_Customer extends Mage_Core_Helper_Abstract
{
    private $aAddressFields = [
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'street' => 'street',
        // 'housenumber-addon' => 'address_2',
        'city' => 'city',
        'zip' => 'postcode',
        'country' => 'country_id'
    ];

    public function bb_doAction($data = null, $baseData) {
        $customerData = $this->getCustomers($data);
        $baseData['customers'] = $customerData;

        $sData = json_encode($baseData, JSON_PRETTY_PRINT);

        $post = [
            'data' => $sData
        ];

        return  Mage::helper('BizzyBee/Base')->Execute('Customer/sync', $post);
    }

    private function getCustomers($data) {
        $aCustomer = [];
        if(!empty($data)) {
            $user = Mage::getModel('customer/customer')->load($data);
            
            if(!empty($user)) {
                $username = explode("@", $user->getEmail(), 2);

                $oUser = new stdClass();
                $oUser->id = $user->getId();
                $oUser->username = $username[0];
                $oUser->email = $user->getEmail();
                $oUser->address = $this->translateAddress($user, 'billing');
                $aCustomer[] = $oUser;
            }
        }

        return $aCustomer;
    }

    private function translateAddress($user, $type) {
        $aData = [];
        $oAddress = new stdClass();
        if($type == 'billing') {
            $aData = $user->getPrimaryBillingAddress();
            if (!empty($aData)) {
                $aData = $aData->getData();
            }
        } elseif ($type == 'shipping') {
            $aData = $user->getPrimaryShippingAddress();
            if (!empty($aData)) {
                $aData = $aData->getData();
            }
        } else {
            return [];
        }

        if (!empty($aData)) {
            foreach($this->aAddressFields as $key => $field) {
                if($key == 'street') {
                    $street = $this->parseStreet($aData[$field]);

                    $oAddress->street = $street['street'];
                    $oAddress->housenumber = $street['number'];
                    $oAddress->{'housenumber-addon'} = $street['addon'];
                } else {
                    $oAddress->{$key} = $aData[$field]; 
                }
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