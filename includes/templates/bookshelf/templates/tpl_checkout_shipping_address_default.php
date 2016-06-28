<?php

error_reporting(E_FATAL);
ini_set('display_errors', 1);
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_shipping_adresss.<br />
 * Allows customer to change the shipping address.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_checkout_shipping_address_default.php 4852 2006-10-28 06:47:45Z drbyte $
 */


?>

<div class="centerColumn" id="checkoutShipAddressDefault">

<?php echo zen_draw_form('checkout_address', zen_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'), 'post', 'onsubmit="return check_form_optional(checkout_address);"'); ?>
<h1 id="checkoutShipAddressDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
<br>
<?php if($how_many > 1){ ?>

<span style="font-size:16px; font-weight:bold; color:">
If you are sending more than 1 gift basket to more than 1 address, enter multiple addresses by clicking the "add address" button below the form, and then you can assign a gift basket to each address after clicking "continue".
</span>

<? } ?>

<?php if ($messageStack->size('checkout_address') > 0) echo $messageStack->output('checkout_address'); ?>

<?php
  if ($process == false || $error == true) {
?>
<!--
<h2 id="checkoutShipAddressDefaultAddress"><?php echo TITLE_SHIPPING_ADDRESS; ?></h2>
-->
<!--     <address class="back"><?php echo zen_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'); ?></address>
    --> 
<!--    <div class="instructions"><?php if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) echo TEXT_CREATE_NEW_SHIPPING_ADDRESS; ?></div>
-->
<br class="clearBoth" />

<?php

    //if ($addresses_count > 1) {
?>
<div style="float:right">
<fieldset class="fieldset_address">
<legend><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></legend>
<?php
      require($template->get_template_dir('tpl_modules_checkout_address_book.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_address_book.php');
?>
</fieldset>
</div>
<?php
     //}

?>

<?php
     if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>
<div style="float:left">
    
     
    
<?php
/**
 * require template to display new address form
 */
  //include($template->get_template_dir('tpl_modules_checkout_new_address.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_new_address.php');

//testing multiple includes.  --elibird 7/23/2014
// going to implement w/ JavaScript.

//nevertheless need to include this page multiple times for each submitted address;
// so, I need a way to set the submitted values in this page for when there's an 
// error submitting multiple addresses.

// guess i'll store how many addresses were submitted in the session, and loop through
// those to display the appropriate values...



if(isset($_SESSION['address_count'])){

   // echo "<br> address count = " . $_SESSION['address_count'];
    
    if($_SESSION['address_count'] !== 0){
        for($i=0;$i<$_SESSION['address_count'];$i++){
            $this_address = $i;
            //echo "<br> INCLUDING TEMPLATE: this_address = $i<br>";
            include($template->get_template_dir('tpl_modules_checkout_new_address.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_new_address.php');
    	    
        }



    } else {
        $this_address = 0;    
        include($template->get_template_dir('tpl_modules_checkout_new_address.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_new_address.php');
    }

    

} else {

//echo "!?";
    
    $this_address = 0;    
    include($template->get_template_dir('tpl_modules_checkout_new_address.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_new_address.php');

}




//include($template->get_template_dir('tpl_modules_checkout_new_address.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_checkout_new_address.php');

?>



</div>

<?php 


if($how_many > 1){ ?>

<input style="float:left; clear:left;" type="button" value="add address" onclick="add_address();">
<?php } ?>

<?php
    }


  }



?>

<div class="buttonRow forward"><?php echo zen_draw_hidden_field('action', 'submit') . zen_image_submit(BUTTON_IMAGE_CONTINUE_NEW, BUTTON_CONTINUE_ALT); ?></div>

<!--
<div class="buttonRow back"><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div>
-->

<?php
  if ($process == true) {
?>
  <div class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

<?php
  }
?>

</form>
</div>
<script>
var i =0;
add_address = function(){
 div = document.getElementById('checkoutNewAddress0');
 clone = div.cloneNode(true);
 i+=1;
 clone.id = 'checkoutNewAddress'+i;
 
 for (x=0;x<clone.children[0].children.length;x++) { if(clone.children[0].children[x].value) clone.children[0].children[x].value=''; }
 
 clone.appendChild(add_remove_button(i));
 document.getElementById('checkoutNewAddress0').parentNode.appendChild(clone);
 

}

add_remove_button = function(i){
    btn = document.createElement('input');
    btn.type='button';
    btn.style='float:right';
    btn.value='remove address';
    btn.onclick = function(){
       this.parentElement.remove();
    }
    return btn;
}

    </script>


