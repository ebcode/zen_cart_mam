<div class="centerColumn" id="checkoutShipping">
	<h1 id="checkoutShippingHeading"><?php echo HEADING_TITLE; ?></h1>
<?php require('tpl_checkout_steps.php'); //Multiple Addresses Mod 

if ($messageStack->size('checkout_multiple_shipments') > 0) echo $messageStack->output('checkout_multiple_shipments'); 

?>

<form method="post" name="multiple_shipments_form"><input name="action" value="process" type="hidden">

<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr class="tableHeading">
		<th colspan="2">
			<?php echo TABLE_HEADING_PRODUCTS; ?>
		</th>
		<th>
			<?php echo TABLE_HEADING_QUANTITY; ?>
		</th>
		<th>
			<?php echo TABLE_HEADING_ADDRESS; ?>
		</th>
		<th >
			<?php echo TABLE_HEADING_SELECT_SHIPPING_METHOD; ?>
		</th>
	</tr>

<?php

//build shipping method select boxes, on the next page though
$k=0;
foreach($all_quotes as $product_id => $quotes){
	$product_address_id=$product_address_ids[$k];
	
	$shipping_method_select = '<select name="shipping_'.$product_address_id.'" style="text-align:right;">'.
								'<option value="">'.SELECT.'</option>';
	
	for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
		
	//echo "<br>".$quotes[$i]['module'];
	
    if($quotes[$i]['error']!='' || sizeof($quotes[$i]['methods'])==0){
		//$shipping_method_select .= '<option value="" style="text-decoration:underline;font-weight:bold;">'.$quotes[$i]['module'].'</option>';
	
	
		//echo "<br>error  =".$quotes[$i]['error'];
		//$shipping_method_select .= 	'<option value="">'.$quotes[$i]['error'].'</option>';

	} else {
         
         $shipping_method_select .= '<option value="" style="text-decoration:underline;font-weight:bold;">'.$quotes[$i]['module'].'</option>';	   

		 for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {
	 	 	
				//echo "<br> input name = ".$quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'];
				//echo "<br> title = ".$quotes[$i]['methods'][$j]['title'];
				//echo "<br> cost:".$currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); 
			
			$shipping_method_select .= 	'<option value="'.$quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'].'"';
			
			//echo "<br>k=$k, shipping_method=".$selected_shipping_methods[$k];
			
			if($selected_shipping_methods[$k]==$quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']){
				$shipping_method_select .= ' selected="selected" ' ;
			}
			
			
			
$shipping_method_select .= '>'.$quotes[$i]['methods'][$j]['title'].': '.($currencies->format(zen_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0)))).'</option>';
	
		 }
	 
	 }
	
	//$checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);  //add another dimension to this for products
}

$shipping_method_select .= '</select>';


	if($k%2==0){
		$tr_class='rowEven';
	} else{
		$tr_class='rowOdd';		
	}


?>
<tr class="<?php echo $tr_class; ?>">
	<td valign="middle"><?php echo $productArray[(int)$product_ids[$k]]['productsImage']; ?>
	    
	    <?php 
//echo "<br>" . $products[$k]['final_price'];
echo "<br>" . get_product_price($product_ids[$k]);  
//echo $product_ids[$k]; ?>
	    
	</td>
	<td valign="middle"><?php echo $productArray[(int)$product_ids[$k]]['productsName']; ?></td>
	
	<td valign="middle" align="center"><?php echo $quantities[$k];?> </td>
	<td valign="middle">
		<fieldset class="address">
			<?php echo zen_address_label($_SESSION['customer_id'], $address_ids[$k], true, ' ', '<br />',true); ?>
		</fieldset>
	</td>
	<td align="center" valign="middle"> <?php echo $shipping_method_select; ?></td>
</tr>

<?php

$k+=1;

}

?>
</table>

<div class="buttonRow forward">
	<?php echo zen_image_submit(BUTTON_PAYMENT_OPTIONS, BUTTON_CONTINUE_ALT, 'name="btn_fwd" onclick="submitFunction('.zen_user_has_gv_account($_SESSION['customer_id']).','.$order->info['total'].')"'); ?>
		<div>
		<?php //echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
		</div>
	</div>
	
	<div class="buttonRow back">
		<?php 
		//Multiple Addresses Mod
		echo zen_image_submit(BUTTON_IMAGE_PREVIOUS_CHECKOUT, BUTTON_PREVIOUS_CHECKOUT_ALT, 'name="btn_back"'); 
		
		?>
		<div>
		<?php //echo TITLE_PREVIOUS_CHECKOUT_PROCEDURE . '<br />' . TEXT_PREVIOUS_CHECKOUT_PROCEDURE; ?>
		</div>
	</div>
	
</form>
</div>
