<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_confirmation.<br />
 * Displays final checkout details, cart, payment and shipping info details.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_checkout_confirmation_default.php 6247 2007-04-21 21:34:47Z wilt $
 */
//echo "<pre>";
//var_dump($_SESSION);
//echo "</pre>";


?>
<script src="./includes/templates/bookshelf/jscript/jquery-latest.min.js"></script>
<script>
    $(document).ready(function(){
         process_form = function(){
             //alert('process form');
             submit = {}; 
            $('textarea').each(function(){
                    submit[this.name] = this.value;
                   //submit.push({nm:vl}); 
            });
            $('input:text').each(function(){
                submit[this.name] = this.value;
               //submit.push({nm:vl}); 
            });
            
            //HTTPS_SERVER is not defined???
            
	   <?php
            $server = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? HTTPS_SERVER : HTTP_SERVER;
                
            ?>

            request = $.ajax({type:'POST', url:'<?php echo $server . DIR_WS_CATALOG; ?>save_msg.php', data:submit, async:false});

                request.done(function( msg ) {
                //$( "#log" ).html( msg );
                });
                request.fail(function( jqXHR, textStatus ) {
                //alert( "Request failed: " + textStatus + '\n\n Please try submitting the form again.' + '\n' + jqXHR.status);
                });
                //alert('ok');
                }

                if(document.forms['checkout_confirmation'].action == 'https://secure.authorize.net/gateway/transact.dll'){
                //create warning message
                /*
                div = document.createElement('div');
                div.style.fontSize = '14px';
                div.style.color = 'black';
                div.style.fontWeight = 'bold';
                div.innerHTML = 'Your order confirmation will open in a new window once you submit this page.  Please close that window once it has finished loading and continue to fill in your credit card details on the following page.   Thanks!';
                document.getElementById('checkoutBillto').appendChild(div);
                
               */
             
               //After filling in the form on that page, you may get an error response from Authorize.net, which you can safely ignore.  Your order will be saved.

               
              
                }

                document.forms['checkout_confirmation'].onsubmit=function(){

                //var y = check_gift_messages();
                //alert(y);

                if(check_gift_messages()){

                    //alert('no form errors');
                     
                     process_form();
                     /*
                     if(document.forms['checkout_confirmation'].action != '<?php echo HTTPS_SERVER . DIR_WS_HTTPS_CATALOG; ?>index.php?main_page=checkout_process'){
    		           //alert('call process form');
    		            process_form();   
                        
                        if(document.forms['checkout_confirmation'].action == 'https://secure.authorize.net/gateway/transact.dll'){
                  window.open('./index.php?main_page=checkout_process&action=confirm', '_blank', 'width=350,height=350,z-lock');
                  }
    		         } */
    		      /*
    		      if(document.forms['checkout_confirmation'].action == 'https://secure.authorize.net/gateway/transact.dll'){
    		      window.open('./index.php?main_page=checkout_process&action=confirm', '_blank', 'width=350,height=350,z-lock');
    		      }
    		      */
        		     //alert('return true');
        		     return true;
        		      
		      
		    } else {
		        //alert('return false');
		      return false;
		    }
		    
		    };
         
         
        });
</script>
<div class="centerColumn" id="checkoutConfirmDefault">

<h1 id="checkoutConfirmDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
<?php
    require ('tpl_checkout_steps.php');
    //Multiple Addresses Mod
 ?>


<?php
    if ($messageStack -> size('redemptions') > 0)
        echo $messageStack -> output('redemptions');
 ?>
<?php
    if ($messageStack -> size('checkout_confirmation') > 0)
        echo $messageStack -> output('checkout_confirmation');
 ?>
<?php
    if ($messageStack -> size('checkout') > 0)
        echo $messageStack -> output('checkout');
 ?>

<?php
if (isset($_GET['msg_err'])) {
    echo "<div class=\"messageStackError larger\">Please enter a gift message and phone # for each basket in your order.</div>";
}
?>

<h2 id="checkoutConfirmDefaultHeadingCart"><?php echo HEADING_PRODUCTS; ?></h2><font size="smaller" color="red">* required</font>

<div class="buttonRow forward"><?php echo '<a href="' . zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; ?></div>
<br class="clearBoth" />

<?php  if ($flagAnyOutOfStock) { ?>
<?php    if (STOCK_ALLOW_CHECKOUT == 'true') {  ?>
<div class="messageStackError"><?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></div>
<?php    } else { ?>
<div class="messageStackError"><?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></div>
<?php    } //endif STOCK_ALLOW_CHECKOUT ?>
<?php  } //endif flagAnyOutOfStock

?>

<?php

    //echo zen_draw_form('checkout_confirmation', $form_action_url, 'post', 'id="checkout_confirmation" onsubmit="submitonce();"');

echo zen_draw_form('checkout_confirmation', $form_action_url, 'post', 'id="checkout_confirmation" ');
?>


      <table border="0" width="100%" cellspacing="0" cellpadding="0" id="cartContentsDisplay">
        <tr class="cartTableHeading">
        <th scope="col" id="ccQuantityHeading" width="30"><?php echo TABLE_HEADING_QUANTITY; ?></th>
        <th scope="col" id="ccProductsHeading"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
<?php
  // If there are tax groups, display the tax columns for price breakdown
  if (sizeof($order->info['tax_groups']) > 1) {
?>
          <th scope="col" id="ccTaxHeading"><?php echo HEADING_TAX; ?></th>
<?php
}
?>
          <th scope="col" id="ccTotalHeading"><?php echo TABLE_HEADING_TOTAL; ?></th>
        </tr>
<?php // now loop thru all products to display quantity and price
            //var_dump($_SESSION['multiple_addresses']);
            $t = 1;
            foreach ($_SESSION['multiple_addresses'] as $address) {

                //var_dump($address);

                foreach ($address['addresses'] as $this_address) {
                    //var_dump($hm);
                    for ($p = 0; $p < $this_address['qty']; $p++) {
                        $msg_index[] = $t;
                    }
                    $t++;
                }

            }

            /*
             foreach($_SESSION['multiple_addresses'] as $address){
             $j=1;

             foreach($address['addresses'] as $hmm){

             var_dump($hmm);

             for($i=0;$i<$hmm['qty'];$i++){
             $list[]=$j;
             }

             $j++;

             }
             }
             */
            //var_dump($list);
            $t_index = 0;
        ?>
<?php for ($i=0, $n=sizeof($order->products); $i<$n; $i++) { 
    
    //var_dump($_SESSION['multiple_addresses'][(int)$order->products[$i]['id']]);
    
    ?>
        <tr class="<?php echo $order -> products[$i]['rowClass']; ?>">
          <td  class="cartQuantity"><?php echo $order -> products[$i]['qty']; ?>&nbsp;x</td>
          <td class="cartProductDisplay"><?php echo $order -> products[$i]['name']; ?>
          <?php  echo $stock_check[$i]; ?>


<?php

   if(isset($_SESSION['byob_id'])){
    ?>
    <div align="left">
    <ul>
    <?php
        $byob_items = $db -> Execute("select bo.quantity, bo.item_id, bi.* from bab_order_items bo left join bab_items bi on bo.item_id = bi.id where bo.bab_order_id = '" . $_SESSION['byob_id'] . "'");

        while (!$byob_items -> EOF) {

            /*
             $this->products[$index]['byob'][$subindex] = array('item_id' =>$byob_items->fields['item_id'],
             'quantity' =>$byob_items->fields['quantity'],
             'item_name' =>$byob_items->fields['nm']);
             $subindex++;
             */
            echo "<li>" . $byob_items -> fields['nm'] . " .......... x " . $byob_items -> fields['quantity'] . "</li>";
            $byob_items -> MoveNext();
        }
 ?>
</ul>
</div>
<?php

    }
?>


<?php // if there are attributes, loop thru them and display one per line
    if (isset($order->products[$i]['attributes']) && sizeof($order->products[$i]['attributes']) > 0 ) {
    echo '<ul class="cartAttribsList">';
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
?>
      <li><?php echo $order -> products[$i]['attributes'][$j]['option'] . ': ' . nl2br(zen_output_string_protected($order -> products[$i]['attributes'][$j]['value'])); ?></li>
<?php
} // end loop
echo '</ul>';
} // endif attribute-info
    ?>
    <div>gift message(s):<font size="smaller" color="red">*</font> </div><br>
    <?php
    
    for ($j=0; $j<$order->products[$i]['qty']; $j++){
        
        if($j>0){
            ?><br>
            <?php
            }
        ?>
        <div style="float:left;clear:left;"><?php echo $msg_index[$t_index]; ?>.</div><textarea style="" name="msg_<?php echo ($order->products[$i]['id']).'_'.$j; ?>" style="margin:4px; float:left;"><?php echo (isset($_SESSION['gift_msg'][$j]['msg']))?$_SESSION['gift_msg'][$j]['msg']:'';?></textarea><br>
    
    recipient's phone:<font size="smaller" color="red">*</font> <input type="text" name="phn_<?php echo ($order->products[$i]['id']).'_'.$j; ?>" value="<?php echo (isset($_SESSION['phn'][$j]['phn']))?$_SESSION['phn'][$j]['phn']:'';?>">
    
        <?php
        $t_index++;

        $js_arr[] = 'msg_' . ($order -> products[$i]['id']) . '_' . $j;
        $js_arr[] = 'phn_' . ($order -> products[$i]['id']) . '_' . $j; 
        }
    ?>
        </td>

<?php // display tax info if exists ?>
<?php if (sizeof($order->info['tax_groups']) > 1)  { ?>
        <td class="cartTotalDisplay">
          <?php echo zen_display_tax_value($order -> products[$i]['tax']); ?>%</td>
<?php    }  // endif tax info display ?>
        <td class="cartTotalDisplay">
          <?php echo $currencies -> display_price($order -> products[$i]['final_price'], $order -> products[$i]['tax'], $order -> products[$i]['qty']);
            if ($order -> products[$i]['onetime_charges'] != 0)
                echo '<br /> ' . $currencies -> display_price($order -> products[$i]['onetime_charges'], $order -> products[$i]['tax'], 1);
        ?>
        </td>
      </tr>
<?php  }  // end for loopthru all products ?>
      </table>
      <hr />

<?php

//require gift_message javascript

foreach ($js_arr as $msg_box) {
    //echo "<br> msg_box = $msg_box";
    $hm .= "'" . $msg_box . "',";

}
$hm = substr($hm, 0, strlen($hm) - 1);
//remove trailing comma

$js_arr = '[' . $hm . ']';
?>

<?php

  if (MODULE_ORDER_TOTAL_INSTALLED) {
    //$order_totals = $order_total_modules->process();  //Multiple Addresses Mod
    
?>
<div id="orderTotals"><?php 
    
    //$output_string = $order_total_modules->output(); 
    //echo $output_string; 
    //var_dump($order_totals_output);
    $zz=0;
        foreach($order_totals_output as $output){
            //if (($zz%2) == 0) {
              //  $rowClass="rowEven";
              //} else {
                $rowClass="rowOdd";
              //}
            ?>
            <div class="<?php echo $rowClass; ?>" style="">
                <?php
                $output = str_replace('Sub-Total', 'Gift Basket', $output);
                //echo str_replace('Sub-Total', 'Gift Basket', $output);
                echo $output;
                ?>
            </div>
            <br class="clearBoth" />
            <?php
            $zz += 1;
            }
    ?></div>
<?php
}



if(isset($_SESSION['multiple_addresses'])){
        ?>
        <br class="clearBoth">  
        <div id="order" style="border-top: double 4px black;"><span class="totalBox larger forward"><?php echo $currencies -> display_price($_SESSION['multiple_addresses_grand_total']); ?></span>
    <span class="lineTitle larger forward"><?php echo GRAND_TOTAL; ?></span></div>
        <?php
        }


    ?>


<div id="checkoutBillto" class="back floatingBox">
<h2 id="checkoutConfirmDefaultBillingAddress"><?php echo HEADING_BILLING_ADDRESS; ?></h2>
<?php if (!$flagDisablePaymentAddressChange) { ?>
<!--
<div class=" "><?php echo '<a href="' . zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '">' . 
zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; ?></div>

<br style="clear:both;">
-->
<?php } 

//PRIMARY_ADDRESS

$edit_billing_address_link = "./index.php?main_page=address_book_process&edit=". $_SESSION['customer_default_address_id'];


?>

<address class="address"><?php echo zen_address_format($order -> billing['format_id'], $order -> billing, 1, ' ', '<br />'); ?></address>
<div class=" "><?php echo '<a href="' . $edit_billing_address_link . '">' . 
zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; ?></div>

<br style="clear:both;">
<?php
$class = &$_SESSION['payment'];
?>

<h3 id="checkoutConfirmDefaultPayment"><?php echo HEADING_PAYMENT_METHOD; ?></h3> 
<h4 id="checkoutConfirmDefaultPaymentTitle"><?php echo $GLOBALS[$class] -> title; ?></h4>

<?php


  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
?>
<div class="important"><?php echo $confirmation['title']; ?></div>
<?php
}



?>
<div class="important">
<?php
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>
<div class="back"><?php echo $confirmation['fields'][$i]['title']; ?></div>
<div ><?php echo $confirmation['fields'][$i]['field']; ?></div>
<?php
}
?>
      </div>
<?php
}




?>

<br class="clearBoth" />
</div>

<?php
  if ($_SESSION['sendto'] != false) {
  	
	
	if(!isset($_SESSION['multiple_addresses'])){
	
?>

<div id="checkoutShipto" class="forward floatingBox">
<h2 id="checkoutConfirmDefaultShippingAddress"><?php echo HEADING_DELIVERY_ADDRESS; ?></h2>
<!--<div class="" style="float:left;">
<?php
//https://www.anythinginabasket.com/index.php?main_page=checkout_payment_address

//$edit_address_link = "./index.php?main_page=address_book_process&edit=". $_SESSION['sendto'];

//echo '<a  style="margin-top:6px; display:inline-block;" href="' . $editShippingButtonLink . '">' . zen_image_button(BUTTON_EDIT$

//echo '<a  style="margin-top:6px; display:inline-block;" href="' . $edit_address_link . '">' . 
//zen_image_button(BUTTON_EDIT_DELIVERY_ADDRESS, BUTTON_ADD_ADDRESS_ALT) . '</a>';

?>

<?php

$edit_address_link = "./index.php?main_page=address_book_process&edit=". $_SESSION['sendto'];

//echo '<a  style="margin-top:6px; display:inline-block;" href="' . $editShippingButtonLink . '">' . zen_image_button(BUTTON_EDIT$

// echo '<a href="' . $edit_address_link . '">' . 
//zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; 

//old buggy:
// echo '<a href="' . $editShippingButtonLink . '">' . 
//zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; 

?>
</div> -->

<address class="address">
	<?php echo zen_address_format($order -> delivery['format_id'], $order -> delivery, 1, ' ', '<br />'); ?>
</address>

<?php
 echo '<a href="' . $edit_address_link . '">' . 
zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; 
?>

<br style="clear:left;">

<?php


    //


    if ($order->info['shipping_method']) {


?>
<h3 id="checkoutConfirmDefaultShipment"><?php echo HEADING_SHIPPING_METHOD; ?></h3>
<h4 id="checkoutConfirmDefaultShipmentTitle"><?php echo $order -> info['shipping_method']; ?></h4>


<?php



}
?>
</div>
<?php


} else {  //isset $_SESSSION['multiple_addresses']
	

	?>
	<div id="checkoutShipto" class="forward floatingBox">
<h2 id="checkoutConfirmDefaultShippingAddress"><?php echo HEADING_DELIVERY_ADDRESS; ?></h2>
<div class="" style="float:left;">
<?php echo '<a href="' . $editShippingButtonLink . '">' . zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; ?>
</div><br style="clear:both;">
<ul style="float:left; margin:0px;padding:0px;">
<?php


$message_index=array();

$zzz=0;
foreach($address_ids as $a_id){
	$zzz+=1;
	?>

	<li style="float:left; margin:0px; list-style:none; padding:0px;">
	<div style="float:left; text-align:left; font-weight:bold;"><?php echo $zzz; ?>.&nbsp;</div>
	<fieldset class="address">
	<?php echo zen_address_label($_SESSION['customer_id'], $a_id, 'true'); ?>
	</fieldset>
	
	</li>
	<?php

    }


?>
</ul>

<!--
<address><?php echo zen_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></address>
<address><?php echo zen_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></address>
<address><?php echo zen_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></address>
<address><?php echo zen_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></address>
-->

<br style="clear:left;">
<?php
    if ($order->info['shipping_method']) {
    	?>

<h3 id="checkoutConfirmDefaultShipment"><?php echo HEADING_SHIPPING_METHOD; ?></h3>
<!--
<h4 id="checkoutConfirmDefaultShipmentTitle"><?php echo $order->info['shipping_method']; ?></h4>
-->
<?php
$zzz=1;
    	foreach($shipping_method_titles as $title){
    		?>
    		<h4 id="checkoutConfirmDefaultShipmentTitle"><?php echo $zzz . ". " . $title; ?></h4>
    		<?php
            $zzz += 1;
            }
        ?>
<?php
}
	?>
	
	</div>
	<?php

    }




    }
?>
<br class="clearBoth" />
<span id="log"></span>
<hr />
<?php
// always show comments
//  if ($order->info['comments']) {


?>

<h2 id="checkoutConfirmDefaultHeadingComments"><?php echo HEADING_ORDER_COMMENTS; ?></h2>
<div class="buttonRow forward"><?php echo '<a href="' . zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT) . '</a>'; ?></div>
<div><?php echo(empty($order -> info['comments']) ? NO_COMMENTS_TEXT : nl2br(zen_output_string_protected($order -> info['comments'])) . zen_draw_hidden_field('comments', $order -> info['comments'])); ?></div>
<br class="clearBoth" />
<?php

//  }



?>




<?php


//echo "<br> WTF? comments = " . $_SESSION['comments'];

/*
<div><?php echo(empty($_SESSION['comments']) ? NO_COMMENTS_TEXT : nl2br(zen_output_string_protected($_SESSION)) . zen_draw_hidden_field('comments', $_SESSION)); ?></div>
<br class="clearBoth" />
*/

?>



<div style="clear:both;"></div>

<?php


//die('here');

if (is_array($payment_modules -> modules)) {
    echo $payment_modules -> process_button();
}



?>
<div class="buttonRow forward">
	
	<?php echo zen_image_submit(BUTTON_PAYMENT_INFORMATION, BUTTON_CONFIRM_ORDER_ALT, 'name="btn_submit" id="btn_submit"'); ?>
	<div>
	<?php //echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
	</div>
</div>

<div class="buttonRow back">
	<?php //echo  '<a href="'.zen_href_link(FILENAME_CHECKOUT_PAYMENT).'">';
    //echo zen_image(BUTTON_IMAGE_PREVIOUS_CHECKOUT,BUTTON_PREVIOUS_CHECKOUT_ALT);
    //echo '</a>';

    //$image_submit = '<input name="btn_back" type="image" src="' . zen_output_string($template->get_template_dir(BUTTON_IMAGE_PREVIOUS_CHECKOUT, DIR_WS_TEMPLATE, $current_page_base, 'buttons/' . $_SESSION['language'] . '/') . BUTTON_IMAGE_PREVIOUS_CHECKOUT) . '" alt="' . zen_output_string(BUTTON_IMAGE_PREVIOUS_CHECKOUT_ALT) . '">';

    $image_submit = '<img src="' . zen_output_string($template -> get_template_dir(BUTTON_IMAGE_PREVIOUS_CHECKOUT, DIR_WS_TEMPLATE, $current_page_base, 'buttons/' . $_SESSION['language'] . '/') . BUTTON_IMAGE_PREVIOUS_CHECKOUT) . '" alt="' . zen_output_string(BUTTON_IMAGE_PREVIOUS_CHECKOUT_ALT) . '">';

    //echo "x=$image_submit";
    echo '<a href="' . zen_href_link(FILENAME_CHECKOUT_PAYMENT) . '">' . $image_submit . '</a>';

    //echo "page = $current_page_base";
	?>
	<div>
	<?php //echo TITLE_PREVIOUS_CHECKOUT_PROCEDURE . '<br />' . TEXT_PREVIOUS_CHECKOUT_PROCEDURE; ?>
	</div>
</div>
</form>

<script>
	    inputs = [<?php echo $hm; ?>];

    function check_gift_messages() {
        err = 0;
        frm = document.forms['checkout_confirmation'];
        for (x in inputs) {
            if (frm[x].value == '') {

                if (!err) {
                    alert('Please enter a gift message and phone # for each basket that you are ordering.');
                }

                err = 1;
                frm[x].style.border = "2px solid red";
            }
        }
        if (err) {
            return false;
        } else {
            return true;
        }
    }

    /*
     document.onload=function(){

     if(document.forms['checkout_confirmation'].action == 'https://secure.authorize.net/gateway/transact.dll'){
     //create warning message
     div = document.createElement('div');
     div.style.fontSize = '14px';
     div.style.color = 'red';
     div.style.fontWeight = 'bold';
     div.innerHTML = 'Your order confirmation will open in a new window once you submit this page.  Please close that window once it has finished loading and continue to fill in your credit card details on the following page.  After filling in the form on that page, you may get an error response from Authorize.net, which you can safely ignore.  Your order will be saved.  Thanks!';
     document.getElementById('checkoutBillto').appendChild(div);
     }

     document.forms['checkout_confirmation'].onsubmit=function(){

     var y = check_gift_messages();
     alert(y);

     if(check_gift_messages()){
     if(document.forms['checkout_confirmation'].action == 'https://secure.authorize.net/gateway/transact.dll'){
     alert('trying window');
     window.open('./index.php?main_page=checkout_process&action=confirm', '_blank', 'z-lock');
     }
     return true;
     } else {
     alert('false');
     return false;
     }

     };
     }
     */


</script>
