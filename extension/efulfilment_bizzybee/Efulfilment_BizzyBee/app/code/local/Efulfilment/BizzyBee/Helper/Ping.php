<?php

class Efulfilment_BizzyBee_Helper_Ping extends Mage_Core_Helper_Abstract {

    public function bb_doAction($data = null, $baseData) {

        $sData = json_encode($baseData, JSON_PRETTY_PRINT);

        $post = [
            'data' => $sData
        ];
      
        return  Mage::helper('BizzyBee/Base')->Execute('ping', $post);
    }
    
}

?>