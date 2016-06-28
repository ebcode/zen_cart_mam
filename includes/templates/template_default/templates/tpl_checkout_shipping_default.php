<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_shipping.<br />
 * Displays allowed shipping modules for selection by customer.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_checkout_shipping_default.php 14807 2009-11-13 17:22:47Z drbyte $
 */

//echo "id= " . $_SESSION['sendto'];


?>
<div class="centerColumn" id="checkoutShipping">

<?php echo zen_draw_form('checkout_address', zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . zen_draw_hidden_field('action', 'process'); ?>

<h1 id="checkoutShippingHeading"><?php echo HEADING_TITLE; ?></h1>
<?php require('tpl_checkout_steps.php'); //Multiple Addresses Mod ?>


<?php if ($messageStack->size('checkout_shipping') > 0) echo $messageStack->output('checkout_shipping'); ?>

<h2 id="checkoutShippingHeadingAddress"><?php echo TITLE_SHIPPING_ADDRESS; ?></h2>
	<div id="checkoutShipto" class="floatingBox back">
<fieldset class="floatingBox "><?php echo zen_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'); ?></fieldset>
</div>
	

<?php //if ($displayAddressEdit) { ?>	
	
	<!--
	<div class="important checkout_shipping_button_info"><?php echo TEXT_CHOOSE_SHIPPING_DESTINATION; ?><br></div>
	-->
	
	<div class="buttonRow">
	    <?php 
	    if($total_count>1) {  //only show multiple address button for carts with more than one item  -- Multiple Addresses Mod
    ?>

<div class="important checkout_shipping_button_info" style="float:right; clear:right"><br><?php //echo TEXT_CHOOSE_MULTIPLE_ADDRESSES;?></div>
<div class="buttonRow" style="margin-top:10px;">
<?php echo '<a href="' . $shipToMultipleAddressesLink . '">' . zen_image_button(BUTTON_IMAGE_MULTIPLE_ADDRESSES, BUTTON_MULTIPLE_ADDRESSES_ALT) . '</a>'; ?>

</div>
<?php } ?>
	    
	    
</div>
<?php //} ?>



<br class="clearBoth" />

<div style="float:right; margin-left:20px; text-align: center;">
<a href="https://www.anythinginabasket.com/images/zone_map_large.png" target="_blank" title="click to enlarge">
<img src="./images/zone_map_checkout.png" style="margin-bottom:5px;"></a><br>
We ship from Huntington, NY<br>( Central Long Island ) 
</div>


<?php 

$edit_address_link = "./index.php?main_page=address_book_process&edit=". $_SESSION['sendto'];

//echo '<a  style="margin-top:6px; display:inline-block;" href="' . $editShippingButtonLink . '">' . zen_image_button(BUTTON_EDIT_DELIVERY_ADDRESS, BUTTON_ADD_ADDRESS_ALT) . '</a>'; 

echo '<a  style="margin-top:6px; display:inline-block;" href="' . $edit_address_link . '">' . 
zen_image_button(BUTTON_EDIT_DELIVERY_ADDRESS, BUTTON_ADD_ADDRESS_ALT) . '</a>';

?>

<?php
  if (zen_count_shipping_modules() > 0) {
?>

<h2 id="checkoutShippingHeadingMethod"><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></h2>

<?php
    if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1) {
?>

<!--
<div id="checkoutShippingContentChoose" class="important"><?php echo TEXT_CHOOSE_SHIPPING_METHOD; ?></div>
-->

<?php
    } elseif ($free_shipping == false) {
?>
<div id="checkoutShippingContentChoose" class="important"><?php echo TEXT_ENTER_SHIPPING_INFORMATION; ?></div>

<?php
    }
?>
<?php
    if ($free_shipping == true) {
?>
<div id="freeShip" class="important" ><?php echo FREE_SHIPPING_TITLE; ?>&nbsp;<?php echo $quotes[$i]['icon']; ?></div>
<div id="defaultSelected"><?php echo sprintf(FREE_SHIPPING_DESCRIPTION, $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . zen_draw_hidden_field('shipping', 'free_free'); ?></div>

<?php
    } else {
      $radio_buttons = 0;
      for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
      // bof: field set
// allows FedEx to work comment comment out Standard and Uncomment FedEx
//      if ($quotes[$i]['id'] != '' || $quotes[$i]['module'] != '') { // FedEx
//echo "module  = " . $quotes[$i]['module'];
//'*Qualified Rate';

if($quotes[$i]['module'] == '*Qualified Rate'){
	//check that zip is in usps zone 1-4
	$first_three_zip = substr($order->delivery['postcode'],0,3);

	//echo "zip = $first_three_zip";
	//echo "zone = " . $ usps_zones['117'];
	$this_zone = $usps_zones[''.$first_three_zip];
        //echo "zone = $this_zone";
	if($this_zone == 1 || $this_zone == 2 || $this_zone == 3 || $this_zone == 4){

	} else {
		continue;
	}
}

      if ($quotes[$i]['module'] != '') { // Standard
?>


<?php
if($quotes[$i]['module'] == 'United States Postal Service'){

$how_many = $_SESSION['cart']->count_contents();

if($how_many > 1){

?><br>
<span style="font-size:larger; color:crimson; font-weight:bold;">The USPS price calculator is currently 
malfunctioning and not displaying the correct shipping prices for multiple baskets. 
Please call us at 1.800.734,4438 for correct shipping amounts. Sorry for the inconvenience.</span>
<?php
}

}
?>
<fieldset>
<legend><?php echo $quotes[$i]['module']; ?>&nbsp;<?php if (isset($quotes[$i]['icon']) && zen_not_null($quotes[$i]['icon'])) { echo $quotes[$i]['icon']; } ?></legend>

<?php
        if (isset($quotes[$i]['error'])) {
?>
      <div><?php echo $quotes[$i]['error']; ?></div>
<?php
        } else {
          for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {
// set the radio button to be checked if it is the method chosen
            $checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);

            if ( ($checked == true) || ($n == 1 && $n2 == 1) ) {
              //echo '      <div id="defaultSelected" class="moduleRowSelected">' . "\n";
            //} else {
              //echo '      <div class="moduleRow">' . "\n";
            }
?>
<?php
            if ( ($n > 1) || ($n2 > 1) ) {
?>
<div class="important forward"><?php echo $currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); ?></div>
<?php
            } else {
?>
<div class="important forward"><?php echo $currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax'])) . zen_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']); ?></div>
<?php
            }
?>

<?php echo zen_draw_radio_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked, 'id="ship-'.$quotes[$i]['id'] . '-' . str_replace(' ', '-', $quotes[$i]['methods'][$j]['id']) .'"'); ?>
<label for="ship-<?php echo $quotes[$i]['id'] . '-' . str_replace(' ', '-', $quotes[$i]['methods'][$j]['id']); ?>" class="checkboxLabel" ><?php echo str_replace('day', 'business day', $quotes[$i]['methods'][$j]['title']); ?></label>
<!--</div>-->
<br class="clearBoth" />
<?php
            $radio_buttons++;
          }
        }
?>

</fieldset>

<?php
if($quotes[$i]['module'] == 'United States Postal Service'){
?>Due to USPS shipping routes, weight minimums and box dimensions, in some instances, rates may be cheaper for more expedited options.  All options will be presented to you.<br>
<?php
}
    }
// eof: field set
      }
    }
?>

<?php
  } else {
?>
<h2 id="checkoutShippingHeadingMethod"><?php echo TITLE_NO_SHIPPING_AVAILABLE; ?></h2>
<div id="checkoutShippingContentChoose" class="important"><?php echo TEXT_NO_SHIPPING_AVAILABLE; ?></div>
<?php
  }
?>
<fieldset class="shipping" id="comments">
<legend><?php echo TABLE_HEADING_COMMENTS; ?></legend>
<?php echo zen_draw_textarea_field('comments', '45', '3'); ?>
</fieldset>

<div class="buttonRow forward">
	
	<?php echo zen_image_submit(BUTTON_PAYMENT_OPTIONS, BUTTON_CONTINUE_ALT); ?>
	
	
	
	<div>
	<?php //echo '<strong>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</strong><br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
	</div>
</div>
<div class="buttonRow back">
	
</div>

</form>
</div>
