<script>
	function update_qty(){
		document.forms['checkout_multiple_addresses'].submit();
	}
	function check_change(inp){
		if(inp.value!=''){
			inp.blur();
		}
	}
</script>
<div class="centerColumn" id="checkoutShipping">
	<h1 id="checkoutShippingHeading"><?php echo HEADING_TITLE; ?></h1>
	
<?php require('tpl_checkout_steps.php'); //Multiple Addresses Mod ?>

<?php 

if ($messageStack->size('checkout_multiple_addresses') > 0) echo $messageStack->output('checkout_multiple_addresses'); ?>

<?php

//echo "shipping_weight=$shipping_weight<br>";

$number_of_addresses=count($addressArray);

//build multiple addresses for number of addresses to ship to
	
?>
<div class="floatingBox back" >
<div style="float:left; margin:10px;">
	<?php echo ' <a href="' . zen_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '&ref=checkout">' . zen_image_button(BUTTON_ADD_SHIPPING_ADDRESS, BUTTON_ADD_SHIPPING_ADDRESS_ALT) . '</a>'; ?>
</div>
<!--	<div style="font-weight:bold; font-size:14px; "> <?php echo TEXT_MULTIPLE_ADDRESSES_USES_ADDRESS_BOOK; ?></div> -->
	
	<?php //echo ' <a href="' . zen_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '&ref=checkout">' . MULTIPLE_ADDRESSES_ADDRESS_BOOK_TEXT . '</a>'; ?>


</div>
<div class="forward important checkout_shipping_button_info" style="width:226px;">
	<div class="" style="float:left; margin:10px;"><?php echo '<a href="' . $shipToSingleAddressLink . '">' . zen_image_button(BUTTON_IMAGE_SINGLE_ADDRESS, BUTTON_IMAGE_SINGLE_ADDRESS_ALT) . '</a>'; ?>
	</div><!--
<?php echo TEXT_GO_BACK_TO_SINGLE_SHIPPING_ADDRESS; ?> -->
	</div>
	<br style="clear:both;">
	
<?php 

echo zen_draw_form('checkout_multiple_addresses', zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL')) . 
zen_draw_hidden_field('action', 'process'); 



?>
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr class="tableHeading">
		<th colspan="2">
			<?php echo TABLE_HEADING_PRODUCTS; ?>
		</th>
		<th>
			<?php echo TABLE_HEADING_QUANTITY; ?>
		</th>
		<th colspan="2" >
			<?php echo TABLE_HEADING_HOW_MANY_ADDRESSES; ?>
		</th>
		<th >
			<?php echo TABLE_HEADING_WHICH_ADDRESS; ?>
		</th>
		<th width="100px">
			<?php echo TABLE_HEADING_HOW_MANY; ?>
		</th>
	</tr>
<?php

//echo "<br>select=$select";
//echo "addres_book_select = $address_book_select<br>";

$x=0;

$number_of_products = count($productArray);

//$count_select = count($address_select);

$address_select_index=0;


foreach ($productArray as $k => $product) {
		
	if($x%2==0){
		$tr_class='rowEven';
	} else{
		$tr_class='rowOdd';		
	}

/* //build shipping method select boxes, on the next page though
	$shipping_method_select = '<select name="shipping_'.$x.'[]" style="text-align:right;">';
	
	for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
		
	//echo "<br>".$quotes[$i]['module'];
		
		$shipping_method_select .= '<option style="text-decoration:underline;font-weight:bold;">'.$quotes[$i]['module'].'</option>';
	
	 for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {
 	 	
			//echo "<br> input name = ".$quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'];
			//echo "<br> title = ".$quotes[$i]['methods'][$j]['title'];
			//echo "<br> cost:".$currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); 
			
		$shipping_method_select .= 	'<option value="'.$quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'].'">'.
		$quotes[$i]['methods'][$j]['title'].': '.($currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0)))).'</option>';

	 }
	
	//$checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);  //add another dimension to this for products
}

$shipping_method_select .= '</select>';
*/

	$qty = TABLE_HEADING_QUANTITY;
	$ship_to = SHIP_TO;	
	//$how_many_addresses = isset($_POST['how_many_addresses_'.$x])?(int)$_POST['how_many_addresses_'.$x]:1;
	 
	 $how_many_addresses = $_SESSION['multiple_addresses'][$product_ids[$x]]['how_many_addresses'];
	 
	 if(!$how_many_addresses){
	 	$how_many_addresses=1;
	 }
	 
	 //echo "<br>how_many_addresses=$how_many_addresses<br>";
	 
	 //need error-handling/input validation, how_many_addresses may not exceed # of address book entries

	 $addresses_o='';
	 
	 //need to adjust quantities based on # of addresses - $product['quantity']
	 
	 for ($j=1;$j<=$how_many_addresses;$j++){
	 	//echo "<br>j=$j<br>";
	 	
	 	$qty_input = '<input name="qty_'.$x.'[]" size="1" ';
	 	
	 	//$qty_input='<input size="1" name="how_many_addresses_'.$x.'" id="how_many_addresses_'.$x.'"  ';

		if($product['quantity']==1){
			//$qty_input.=' disabled="true" value="1"';
			//$button='&nbsp;';
            $qty_input = "1";
		} else {
			//$qty_input.=' value="'.$product['quantity'].'"';
			
			//echo "<br>count=".count($_SESSION['multiple_addresses'][$x]['addresses']);
			
			if(is_array($_SESSION['multiple_addresses'][$product_ids[$x]]['addresses'][0])){
				//echo "<br>140: is_array, value = ". $_SESSION['multiple_addresses'][$product_ids[$x]]['addresses'][$j-1]['qty'];
				$qty_input.=' value="'.$_SESSION['multiple_addresses'][$product_ids[$x]]['addresses'][$j-1]['qty'].'"';
			} else {
				
				if(isset($_SESSION['multiple_addresses'][$product_ids[$x]]['addresses']['qty'])){			
					$qty_input.=' value="'.$_SESSION['multiple_addresses'][$product_ids[$x]]['addresses']['qty'].'"';
				} else {
					$qty_input.=' value="'.$product['quantity'].'"';
				}
			}
			
			//echo "<br>x=$x<br>";
			//echo "<br>j=$j<br>";
			//echo "<br>should be: ".$_SESSION['multiple_addresses'][$x]['addresses']['qty'].'<br>';
			//$button=$buttonUpdate;
		}

        if($qty_input !== "1"){
            $qty_input .=">";
        } else {

		  
	 	
		}
		//if($count_select >1){
		//	$address_book_select = $address_select[$address_select_index];
		//} else {
			$address_book_select = $address_select[$address_select_index];
		//}	
		
		//echo "<br>address_book_select = $address_book_select<br>";
		
		$address_select_index+=1;
		
		
		$addresses_tables = <<<EOT
		<table border="0" width="100%">
				<tr class="{$tr_class}">
				<td align="center" valign="middle">{$ship_to} {$address_book_select}</td>
				<td align="center" valign="middle" width="100px">{$qty} {$qty_input}
				</td></tr>
		</table>
EOT;

		$addresses_o[$x] .= $addresses_tables;		
		//echo "<br>addresses_o $j =$addresses_tables<br>";
	 }

	$qty_input='<input size="1" onchange="update_qty();" onkeyup="check_change(this);" name="how_many_addresses_'.$x.'" id="how_many_addresses_'.$x.'"  ';
	
	if($product['quantity']==1){
		//$qty_input.=' disabled="true" value="1"';
		$button='&nbsp;';
        
        $qty_input = "1";

	} else {
		$qty_input.=' value="'.$how_many_addresses.'"';
		//$button=$buttonUpdate;
		$button='&nbsp;';
	}

    if($qty_input !== "1"){
	  $qty_input .=">";
    }
	 
	$out = <<<EOF
	<tr class="{$tr_class}">
		<td valign="middle" valign="middle">{$product['productsImage']}</td><td valign="middle">
			{$product['productsName']}<br>
            {$products[$k]['final_price']}
		</td>
		<td align="center" valign="middle">
			{$product['quantity']}
		</td>
		<td align="center" valign="middle">
			 {$qty_input}
		</td>
		<td align="left" valign="middle">
			{$button}
		</td>
		<td colspan="2" valign="middle">
			{$addresses_o[$x]}	
		</td>
	</tr>
EOF;

	$o = $o . $out;
	
	$x++;

}

echo $o;

?>
	</table>
	
<fieldset class="shipping" id="comments">
<legend><?php echo TABLE_HEADING_COMMENTS; ?></legend>
<?php echo zen_draw_textarea_field('comments', '45', '3'); ?>
</fieldset>	
	
	<div class="buttonRow forward">
	<?php echo zen_image_submit(BUTTON_DELIVERY_OPTIONS, BUTTON_CONTINUE_ALT, 'name="btn_fwd" onclick="submitFunction('.zen_user_has_gv_account($_SESSION['customer_id']).','.$order->info['total'].')"'); ?>
		<div>
		<?php //echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
		</div>
	</div>
	<!--
	<div class="buttonRow back">
		<?php 
		//Multiple Addresses Mod
		//echo zen_image_submit(BUTTON_IMAGE_PREVIOUS_CHECKOUT, BUTTON_PREVIOUS_CHECKOUT_ALT, 'name="btn_back"'); 
		
		?>
		<div>
		<?php //echo TITLE_PREVIOUS_CHECKOUT_PROCEDURE . '<br />' . TEXT_PREVIOUS_CHECKOUT_PROCEDURE; ?>
		</div>
	</div>
	-->
</form>
</div>