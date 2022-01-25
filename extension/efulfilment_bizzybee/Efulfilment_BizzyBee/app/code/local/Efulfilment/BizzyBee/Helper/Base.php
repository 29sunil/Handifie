<?php
class Efulfilment_BizzyBee_Helper_Base extends Mage_Core_Helper_Abstract
{
    protected $sURL = 'https://bizzybee.ws/API/';
    protected $oParent = null;

    public function __construct() {
     
    }

    public function getBase() { 
        $identity = Mage::getStoreConfig('module/bizzybee/identity');
        $auth = Mage::getStoreConfig('module/bizzybee/auth');
        $sonse = Mage::getStoreConfig('module/bizzybee/sonse');
        $site_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'index.php/BizzyBee';
        
        
        $data = [
            'identity' =>  $identity,
            'authentication' => $auth,
            'sonce' =>  $sonse,
            'domain' => $site_url
        ];
        return $data;
    }

    public function Execute($type, $post) {

        file_put_contents('C:/logs/post.log', print_r($post, true) . PHP_EOL . PHP_EOL, FILE_APPEND);

        $ch = curl_init($this->sURL . $type);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);

        file_put_contents('C:/logs/debug.log', $output . PHP_EOL . PHP_EOL, FILE_APPEND);

        return json_decode($output);
    }
}