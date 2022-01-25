<?php
class Efulfilment_BizzyBee_Helper_Product extends Mage_Core_Helper_Abstract
{

    public function bb_doAction($data = null, $baseData) {
       
        $productData = $this->getProduct($data);
        $baseData['items'] = $productData;

        $sData = json_encode($baseData, JSON_PRETTY_PRINT);

        $post = [
            'data' => $sData
        ];

        return  Mage::helper('BizzyBee/Base')->Execute('Product/sync', $post);
    }

    private function getProduct($data) {

        $product = Mage::getModel('catalog/product')->load($data);
        $sType = $product->getTypeId();
        $aProductData = [];

        $sSku = $product->getSku();
        if(!empty($sSku)) {
            $aProductData[] = [
                'sku' => $sSku,
                'brand' => '',
                'brandgroup' => '',
                'vatcode' => 1,
                'costprice' => '',
                'price' => number_format($product->getPrice(), 2, '.', ''),
                'description' => $product->getName(),
                'image' => $product->getImageUrl()
            ];
        }
       
        return $aProductData;
    }
}