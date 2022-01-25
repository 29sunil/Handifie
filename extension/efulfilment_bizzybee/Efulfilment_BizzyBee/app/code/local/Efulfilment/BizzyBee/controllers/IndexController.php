<?php
class Efulfilment_BizzyBee_IndexController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
        $this->loadLayout();

        // get the base to make ping request
        $base = Mage::helper('BizzyBee/Base')->getBase();
        // do ping request
        $result = Mage::helper('BizzyBee/Ping')->bb_doAction('Ping', $base);

        // if we get the sonse out of request, save the retrieved sonse
        if(isset($result->sonce)) {
            Mage::getConfig()->saveConfig('module/bizzybee/sonse', $result->sonce, 'default', 0);
        }

        //get carriers for future setting
        $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
      
        foreach($carriers as $code => $method){
            $carriersData[$code] = array(
                "title"     => Mage::getStoreConfig("carriers/$code/title"),
                "methods"   => $method->getMethods(),
             );
        }
     
        // get the api keys
        $identity = Mage::getStoreConfig('module/bizzybee/identity');
        $auth = Mage::getStoreConfig('module/bizzybee/auth');
        $sonse = Mage::getStoreConfig('module/bizzybee/sonse');

        // asign the values to phtml
        Mage::register('BizzyBee_identity', $identity);
        Mage::register('BizzyBee_auth', $auth);
        Mage::register('BizzyBee_ping', $result);
        Mage::register('BizzyBee_sonse', $sonse);

        // $this->renderLayout(); 
        $this->_setActiveMenu('Bizzybee_menu')->renderLayout();      
    }   

    public function saveAction()
	{
        // get the new params
        $identity = $this->getRequest()->getParam('bizzybee_identity');
        $auth = $this->getRequest()->getParam('bizzybee_authentication');
        $sonse = $this->getRequest()->getParam('bizzybee_sonse');
        
        //save the new params
        Mage::getConfig()->saveConfig('module/bizzybee/identity', $identity, 'default', 0);
        Mage::getConfig()->saveConfig('module/bizzybee/auth', $auth, 'default', 0);
        Mage::getConfig()->saveConfig('module/bizzybee/sonse', $sonse, 'default', 0);

        $this->_forward('index');

	}

  
}