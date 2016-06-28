<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_shipping.php 4042 2006-07-30 23:05:39Z drbyte $
 */

define('NAVBAR_TITLE_1', 'Checkout');
define('NAVBAR_TITLE_2', 'Delivery Method');

define('HEADING_TITLE', 'Delivery Information'); //Multiple Addresses Mod

define('TABLE_HEADING_SHIPPING_ADDRESS', 'Delivery Address');
define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'Your order will be shipped to the address at the left or you may choose a different shipping address by clicking the button below.');

//define('TITLE_SHIPPING_ADDRESS', 'Shipping Information:');

define('TITLE_SHIPPING_ADDRESS', '');

define('TABLE_HEADING_SHIPPING_METHOD', 'Delivery Method:');
define('TEXT_CHOOSE_SHIPPING_METHOD', 'Please select the preferred delivery method to use on this order.');
define('TITLE_PLEASE_SELECT', 'Please Select');
define('TEXT_ENTER_SHIPPING_INFORMATION', 'This is currently the only delivery method available to use on this order.');
define('TITLE_NO_SHIPPING_AVAILABLE', 'Not Available At This Time');
define('TEXT_NO_SHIPPING_AVAILABLE','<span class="alert">Sorry, we are not delivering to your region at this time.</span><br />Please contact us for alternate arrangements.');

define('TABLE_HEADING_COMMENTS', 'Special Instructions or Comments About Your Order');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continue to Step 2');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- choose your payment method.');

// when free shipping for orders over $XX.00 is active
define('FREE_SHIPPING_TITLE', 'Free Shipping');
define('FREE_SHIPPING_DESCRIPTION', 'Free shipping for orders over %s');
  
  
    
//Multiple Addresses Mod
define('TEXT_CHOOSE_MULTIPLE_ADDRESSES', 'If you want to ship this order to multiple addresses, please click the <em>Ship To Multiple Addresses</em> button.');

define('ERROR_NO_SHIPPING_METHOD','Please select a shipping method.');

?>
