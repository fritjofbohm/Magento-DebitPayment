<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Mage_Debit
 * @copyright  2011 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright  2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Debit_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * _construct
     * 
     * Construct payment form block and set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('debit/form.phtml');
    }

    /**
     * getBankName
     * 
     * Returns the account bankname if applicable from the payment info instance
     * 
     * @return string Bankname/Error
     */
    public function getBankName()
    {
        $blz = $this->getAccountBLZ();
        if (empty($blz)) {
            return $this->__('-- will be filled in automatically --');
        }
        $bankName = Mage::helper('debit')->getBankByBlz($blz);
        if ($bankName == null) {
            $bankName = $this->__('not available');
        }
        return $bankName;
    }

    /**
     * getAccountBLZ
     * 
     * Returns the account blz from the payment info instance
     * 
     * @return string BLZ
     */
    public function getAccountBLZ()
    {
    	if ($data = $this->getInfoData('cc_type')) {
	    	return $data;
	    } elseif ($data = $this->_getAccountData('debit_payment_acount_blz')) {
	    	return $data;
	    } else {
	    	return '';
	    }
    }

    /**
     * getAccountName
     * 
     * Returns the account name from the payment info instance
     * 
     * @return string Name
     */
    public function getAccountName()
    {
        if ($data = $this->getInfoData('cc_owner')) {
            return $data;
        }
        return $this->_getAccountData('debit_payment_acount_name');
    }

    /**
     * getAccountNumber
     * 
     * Returns the account number from the payment info instance
     * 
     * @return string Number
     */
    public function getAccountNumber()
    {
	    if ($data = $this->getInfoData('cc_number')) {
	    	return $data;
	    } elseif ($data = $this->_getAccountData('debit_payment_acount_number')) {
	    	return $data;
	    } else {
	    	return '';
	    }
    }

    /**
     * _getAccountData
     * 
     * Returns the specific value of the requested field from the
     * customer model.
     * 
     * @param string $field Attribute to get
     */
    protected function _getAccountData($field)
    {
        if (!Mage::getStoreConfigFlag('payment/debit/save_account_data')) {
            return '';
        }
        $data = $this->getCustomer()->getData($field);
        if (strlen($data) == 0) {
            return '';
        }
        return $this->htmlEscape($data);
    }

    /**
     * getCustomer
     * 
     * Returns the current customer
     * 
     * @return Mage_Customer_Model_Customer Customer
     */
    public function getCustomer()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        }
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * getCheckoutValidBlz
     * 
     * Returns the config setting if checkout is only allowed with a valid BLZ
     * 
     * @return boolean true/false
     */
    public function getCheckoutValidBlz() {
    	return Mage::getStoreConfigFlag('payment/debit/checkout_valid_blz');
    }
}