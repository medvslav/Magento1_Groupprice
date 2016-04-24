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
 * Product type price model
 *
 * @category    Medvslav
 * @package     Medvslav_Groupprice
 * @author      Medvslav
 */
class Medvslav_Groupprice_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
        $groupPrices = $product->getData('group_price');
        if (is_null($groupPrices)) {
            $attribute = $product->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $groupPrices = $product->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return $product->getData('price');
        }

        if ($product->getCustomerGroupId()) {
            $customerGroup = $product->getCustomerGroupId();
        }
        $customerGroup =  Mage::getSingleton('customer/session')->getCustomerGroupId();

        $matchedPrice = $product->getData('price');
        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup ) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }
        return $matchedPrice;
    }

    /**
     * Apply group price for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    protected function _applyGroupPrice($product, $finalPrice)
    {
        $groupPrice = $product->getGroupPrice();
        if (is_numeric($groupPrice)) {           
            $finalPrice = $groupPrice;
        }
        return $finalPrice;
    }

    /**
     * Get product group price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getGroupPrice($product)
    {

        $groupPrices = $product->getData('group_price');
        if (is_null($groupPrices)) {
            $attribute = $product->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $groupPrices = $product->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return $product->getPrice();
        }

        $customerGroup = $this->_getCustomerGroupId($product);

        $matchedPrice = $product->getPrice();
        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup ) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }
        return $matchedPrice;
    }
}
