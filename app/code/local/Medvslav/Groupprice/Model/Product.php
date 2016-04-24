<?php
/**
 * Medvslav_Groupprice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Medvslav
 * @package        Medvslav_Groupprice
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Catalog product model
 *
 * @category    Medvslav
 * @package     Medvslav_Groupprice
 * @author      Medvslav
 */
class Medvslav_Groupprice_Model_Product extends Mage_Catalog_Model_Product
{
    /**
     * Get product price throught type instance
     *
     * @return decimal
     */
    public function getPrice()
    {
        $groupPrices = $this->getData('group_price');
        if (is_null($groupPrices)) {
            $attribute = $this->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($this);
                $groupPrices = $this->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return $this->getPriceOld();
        }

        if ($this->getCustomerGroupId()) {
            $customerGroup = $this->getCustomerGroupId();
        }
        $customerGroup =  Mage::getSingleton('customer/session')->getCustomerGroupId();

        $matchedPrice = $this->getPriceOld();
        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup ) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }
        return $matchedPrice;

    }
    
     /**
     * Old method get product price throught type instance
     *
     * @return decimal
     */
    public function getPriceOld()
    {
        if ($this->_calculatePrice || !$this->getData('price')) {
            return $this->getPriceModel()->getPrice($this);
        } else {
            return $this->getData('price');
        }       
    }   

    /**
     * Get product final price
     *
     * @param double $qty
     * @return double
     */
    public function getFinalPrice($qty=null)
    {
        return $this->getPriceModel()->getFinalPrice($qty, $this);
    }
}
