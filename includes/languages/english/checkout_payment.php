<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_payment.php 4087 2006-08-07 04:46:08Z drbyte $
 */

define('NAVBAR_TITLE_1', 'Checkout');
define('NAVBAR_TITLE_2', 'Payment Options');

define('HEADING_TITLE', 'Payment Options');

define('TABLE_HEADING_BILLING_ADDRESS', 'Billing Address');
define('TEXT_SELECTED_BILLING_DESTINATION', '<span style="color:red">Your billing address is shown above. The billing address should match the address on your credit card statement. You can change the billing address by clicking the <em>Edit Address</em> button.</span>');
define('TITLE_BILLING_ADDRESS', 'Billing Address');

define('TABLE_HEADING_PAYMENT_METHOD', 'Payment Method');
define('TEXT_SELECT_PAYMENT_METHOD', 'Please select a payment method for this order.');
define('TITLE_PLEASE_SELECT', 'Please Select');
define('TEXT_ENTER_PAYMENT_INFORMATION', '');
define('TABLE_HEADING_COMMENTS', 'Special Instructions or Order Comments');

define('TITLE_NO_PAYMENT_OPTIONS_AVAILABLE', 'Not Available At This Time');
define('TEXT_NO_PAYMENT_OPTIONS_AVAILABLE','<span class="alert">Sorry, we are not accepting payments from your region at this time.</span><br />Please contact us for alternate arrangements.');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', '<strong>Continue to Step 3</strong>');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- to confirm your order.');

define('TABLE_HEADING_CONDITIONS', '<span class="termsconditions">Terms and Conditions</span>');
define('TEXT_CONDITIONS_DESCRIPTION', '<span class="termsdescription">Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . zen_href_link(FILENAME_CONDITIONS, '', 'SSL') . '"><span class="pseudolink">here</span></a>.');
define('TEXT_CONDITIONS_CONFIRM', '<span class="termsiagree">I have read and agreed to the terms and conditions bound to this order.</span>');

define('TEXT_CHECKOUT_AMOUNT_DUE', 'Total Amount Due: ');
define('TEXT_YOUR_TOTAL','Your Total');


/* Multiple Addresses Mod */
define('TITLE_PREVIOUS_CHECKOUT_PROCEDURE', '<strong>Return to Step 1</strong>');
define('TEXT_PREVIOUS_CHECKOUT_PROCEDURE', '- to confirm your delivery information.');

define('TITLE_PREVIOUS_CHECKOUT_PROCEDURE_MULTIPLE', '<strong>Return to Step 1.2</strong>');
define('TEXT_PREVIOUS_CHECKOUT_PROCEDURE_MULTIPLE', '- to confirm your delivery information.');

define('GRAND_TOTAL', 'Grand Total:');

?>