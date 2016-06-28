<?php

if($_SERVER['REMOTE_ADDR'] != ''){

}

/**
 * Common Template - tpl_main_page.php
 *
 * Governs the overall layout of an entire page<br />
 * Normally consisting of a header, left side column. center column. right side column and footer<br />
 * For customizing, this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * - make a directory /templates/my_template/privacy<br />
 * - copy /templates/templates_defaults/common/tpl_main_page.php to /templates/my_template/privacy/tpl_main_page.php<br />
 * <br />
 * to override the global settings and turn off columns un-comment the lines below for the correct column to turn off<br />
 * to turn off the header and/or footer uncomment the lines below<br />
 * Note: header can be disabled in the tpl_header.php<br />
 * Note: footer can be disabled in the tpl_footer.php<br />
 * <br />
 * $flag_disable_header = true;<br />
 * $flag_disable_left = true;<br />
 * $flag_disable_right = true;<br />
 * $flag_disable_footer = true;<br />
 * <br />
 * // example to not display right column on main page when Always Show Categories is OFF<br />
 * <br />
 * if ($current_page_base == 'index' and $cPath == '') {<br />
 *  $flag_disable_right = true;<br />
 * }<br />
 * <br />
 * example to not display right column on main page when Always Show Categories is ON and set to categories_id 3<br />
 * <br />
 * if ($current_page_base == 'index' and $cPath == '' or $cPath == '3') {<br />
 *  $flag_disable_right = true;<br />
 * }<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_main_page.php 7085 2007-09-22 04:56:31Z ajeh $
 */

// the following IF statement can be duplicated/modified as needed to set additional flags
  if (in_array($current_page_base,explode(",",'list_pages_to_skip_all_right_sideboxes_on_here,separated_by_commas,and_no_spaces')) ) {
    $flag_disable_right = true;
  }


  $header_template = 'tpl_header.php';
  $footer_template = 'tpl_footer.php';
  $left_column_file = 'column_left.php';
  $right_column_file = 'column_right.php';
  $body_id = ($this_is_home_page) ? 'indexHome' : str_replace('_', '', $_GET['main_page']);
  
?>
<body id="<?php echo $body_id . 'Body'; ?>"<?php if($zv_onload !='') echo ' onload="'.$zv_onload.'"'; ?>>
<div id="err" style="width:300px; border:1px dashed black; display:none; float:right; position:absolute; top:0px right:0px; z-index:10;">

</div>
<form name="cart_quantity" action="./index.php?main_page=product_info&amp;cPath=36&amp;products_id=180&amp;number_of_uploads=0&amp;action=add_product" method="post" enctype="multipart/form-data" onsubmit="checking_out=1;">
<div style="border:1px dashed red; height:50px;width:50px; position:absolute;" id="img_container" class="hidden">
<img id="hover_image" src="">
</div>

<style>

.cat_ul{
margin: 0;
padding: 0;
}

.cat_ul li{
float:left;
margin:0;
list-style-type:none;
margin-left:2px;
margin-right:2px;
font-weight:bold;
}

.active_li{
background-color: #FFEE55;
}

.basket_item{
	width:180px;
	margin:3px;
	text-align:center;
}

.bskt_items{

}

.bskt_items div{
float:left;

max-width:90px;

text-align: left;
}

.bskt_items img{
float:left;
vertical-align:text-top;
margin:1px;
max-width:40px;
max-height:26px;
}

.bskt_items input{
height:10px;
width:12px;
text-align:center;
font-size:9px;
float:left;
}

.bab{

}

.item_list legend{
max-width:178px;
}

body{
font-size:12px;

}

#mainWrapper{
height:101%;
}

html {
	overflow-y: scroll; 
}

</style>

<script src="./includes/templates/bookshelf/jscript/jquery-latest.min.js"></script>
<script src="./includes/templates/bookshelf/jscript/jquery.urldecoder.min.js"></script>

<div id="mainWrapper">
<?php
 /**
  * prepares and displays header output
  *
  */
  if (CUSTOMERS_APPROVAL_AUTHORIZATION == 1 && CUSTOMERS_AUTHORIZATION_HEADER_OFF == 'true' and ($_SESSION['customers_authorization'] != 0 or $_SESSION['customer_id'] == '')) {
    $flag_disable_header = true;
  }
/*
  require($template->get_template_dir('tpl_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_header.php');?>
*/
  ?>
<div style="width:1000px; background-color:#fff;" class="byob" >
<!-- style="position:fixed; background-color:#fff; top:0;"  -->
<div style="position:fixed; background-color:#fff; top:0;">

<div id="headsup">

<div style="float:left; width:194px; height:65px;">
<a href="https://anythinginabasket.com" style="border:0;">
<img src="includes/templates/bookshelf/images/new_tisket_small_brown.png" alt="A Tisket A Tasket Anything In A Basket" style="border:0;"><br>
</a>

<div align="center" style="position:relative;">
631.385.0001
<div style="font-size:14px; margin-top:6px; font-weight:bold;">Build Your Own Basket</div>
<div style="font-variant:small-caps; font-size:13px;"><a href="./byob.php?action=instructions" target="_blank">instructions</a></div>
</div>
 
</div>
<!--  <table width="100%" border="0" cellspacing="0" cellpadding="0" id="contentMainWrapper">
  <tr>
  -->
  
  <?php 
  
  $basket_ids = array(180,175,176,177,178);
  
  $q2 = "select * from bab_baskets where ac=1 order by pt";
  $rs2 = mysql_query($q2) or die(__LINE__.": ".mysql_error());
  
  $bi='';
  
  $i=0;
  $z=0;
  
  $bo='';
  $z =0;
  while($row2 = mysql_fetch_array($rs2, MYSQL_ASSOC)){
  	//var_dump($row);
  	$js .= 'basket['.$row2['id'].'] = {"id":"'.$row2['id'].'","nm":"'.$row2['nm'].'","pt":"'.$row2['pt'].'","im":"'.$row2['im'].'","pr":"'.$row2['pr'].'"};'."\n";
  	$active='class="basket"';
  	$selected='';
  	
  	$baskets[]=$row2;
  	
  	$js_basket_ids .= 'basket_ids['.$row2['id'].'] = '.$basket_ids[$z].';'."\n";
  	
	if($z==0){
	$active='class="active basket"';
	$active_basket_id=$row2['id'];
	$selected='checked="checked"';
	$basket_points=$row2['pt'];
	}
	$z++;
	$bo .= <<<EOT
<fieldset $active id="bsk_{$row2['id']}"><legend><input type="radio" $selected name="o" id="o{$row2['id']}" value="1" onclick="return change_basket(basket[{$row2['id']}])"><label for="o{$row2['id']}" style="cursor:pointer; display:inline-block; width:62px;"> {$row2['nm']}</label></legend><label for="o{$row2['id']}" style="cursor:pointer; display:inline;padding:0;margin:0;">&#36;{$row2['pr']} &ndash; {$row2['pt']}pts<br><img src="{$link}/bab/images/{$row2['im']}"></label></fieldset>
EOT;
  
  
  }
  
  
  
  //categories
  //$q = "select distinct cat_id from bab_items";
  
  $q = "select * from bab_categories where id in (select cat_id from bab_items where ac=1) and active=1;";
  $rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
  $z=1;
  while($row = mysql_fetch_array($rs, MYSQL_ASSOC)){
	//$categories[] = $row['cat_id'];
	$categories[$row['id']] = array('name'=>$row['id'],'pages'=>1, 'cat_name'=>$row['cat_name']);
	if($z==1){
		$active_cat_id=$row['id'];
	} else {
		$z=0;
	}
	/*
	$q2 = "select * from bab_categories where id = '".$row['cat_id']."'";
	$rs2= mysql_query($q) or die(__LINE__.": ".mysql_error());
	$row2 = mysql_fetch_array($rs, MYSQL_ASSOC);
	*/
	
	//$categories[$row['cat_id']] = array('name'=>$row['cat_id'],'pages'=>'', 'cat_name'=>$row2['cat_name']);
	
	
  }

  $active_cat = $categories[$active_cat_id];
  
  //$q = "select * from bab_items where ac=1 order by cat_id, nm";
  
  $q = "select * from bab_items where ac=1 order by nm";
  
  $rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
  
  $bi='';
  
  $i=0;
  $z=0;
  
  $items_per_page = 2;
  $pages=0;
  $item=0;
  $last_cat = '';
  
  $first_id=0;
  
  while($row = mysql_fetch_array($rs, MYSQL_ASSOC)){  //need to change this to not grab everything at once
	

	if(!$first_id){
		$first_id = $row['id'];
	}
	//$categories[$row['cat_id']]['pages']=1;

	if($row['cat_id']!=$last_cat){
		$last_cat=$row['cat_id'];
		$pages=0;
		$z=0;
		$item=0;
	}

	//var_dump($row);
	$item+=1;
	$i+=1;
	
	if($z>1){  //alternating 2 trs to put into two tables
		$z=0;
	}
	
	if($item == $items_per_page+1){
		//$pages+=1;
		$item=1;
		//$categories[$row['cat_id']]['pages']=$pages;
		//$categories[$row['cat_id']]['pages']++;
	}
	
	/*
	$bi[$row['cat_id']][$pages][$z] .= <<<EOT
<tr>
 		<td align="center" width="114px">
 		
 		<img src="{$link}/bab/images/{$row['im']}" height="78px" align="middle" onclick="document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; ;">
 		<br>
	    {$row['pt']} pts
 		</td>
	    
	    <td width="78px">{$row['nm']}
	    </td>
 		<!--
	    <td></td>
	    <td></td>
	    -->
	    <td><input size="2" style="width:20px" value="0" name="item_{$row['id']}"></td>
</tr>
EOT;
*/
	
/*	
	$bi[$row['cat_id']][$pages][$z] .= <<<EOT
	<tr><td>
		<fieldset><legend>{$row['nm']} - {$row['pt']}pts</legend>
  			<img src="{$link}/bab/images/{$row['im']}" height="78px" align="middle" onclick="document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; ;"> 		
  		</fieldset>
	</td>
	</tr>
EOT;
*/
	
	//$ds = addslashes($row['ds']);
	
	//$ds = addslashes($row['ds']);
	
	$ds = urlencode($row['ds']);
	
	//$ds = str_replace("'","\'",$row['ds']);  //description
	 
	//$ds = str_replace("'","",$row['ds']);  //description
	
	//$ds = str_replace("\\"," ",$ds); 
	 
	$ds = str_replace(array("\r\n","\n","\r")," ",$ds);
	
	//$ds = str_replace("'","&#39;",$row['ds']);  //description
	
	//$ds = str_replace('"',"\u201c",$row['ds']);  //description
	
	//$ds = "'$ds'"; //escape double-quotes " w/ \"
	
	//$ds = urlencode($ds);
	
	$bi[$row['cat_id']][] .= <<<EOT
		<a style="cursor:pointer;"
		onclick="if(fullscreen)show_items_only();document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'.replace('sm.jpg','jpg'); 
			document.getElementById('product_description').innerHTML=$.url.decode('{$ds}'); 
			document.getElementById('product_pts').innerHTML={$row['pt']};
			active_basket_item = basket_items[{$row['id']}];
			" 
			
		onfocus="if(fullscreen)show_items_only();document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; 
			document.getElementById('product_description').innerHTML=$.url.decode('{$ds}'); 
			document.getElementById('product_pts').innerHTML={$row['pt']};
			active_basket_item = basket_items[{$row['id']}];" >
			
			<fieldset class="basket_item" onfocus="document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; document.getElementById('product_description').innerHTML=$.url.decode('{$ds}'); return false;"><legend>{$row['nm']} - {$row['pt']}pts</legend>
  			<img src="{$link}/bab/images/{$row['im']}" height="78px" align="middle" class="basket_item_image" onmouseover="$('#img_container').toggleClass('hidden'); document.getElementById('hover_image').src='{$link}/bab/images/{$row['im']}'.replace('sm.jpg','jpg');" onmouseout="$('#img_container').toggleClass('hidden');">
  		</fieldset>
		</a>
EOT;
	
	//$js2 .= 'basket_items['.$row['id'].'] = {"id":"'.$row['id'].'","ds":'.$ds.',"im":"'.$row['im'].'", "pt":"'.$row['pt'].'", "nm":"'.$row['nm'].'"};'."\n";
	
	
	
	$row['nm'] = str_replace("'","&#39;",$row['nm']);  //replace single quote
	
	$row['nm'] = str_replace('"',"&#34;",$row['nm']);  //replace double quote
	
	$js2 .= "basket_items[{$row['id']}] = {'id':'{$row['id']}','ds':$.url.decode('{$ds}'),'im':'{$row['im']}', 'pt':'{$row['pt']}', 'nm':$.url.decode('{$row['nm']}')};\n";
	
	/*
	echo <<<EOT
	<tr><td>
		<fieldset><legend>{$row['nm']} - {$row['pt']} pts</legend>
  			<img src="{$link}/bab/images/{$row['im']}" height="78px" align="middle" onclick="document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; ;"> 		
  		</fieldset>
	</td>
	</tr>
EOT;
*/
	    $z+=1;
	
}


//var_dump($categories);

/*
mysql_data_seek($rs,0);
$row = mysql_fetch_array($rs, MYSQL_ASSOC);
*/

//echo $bi;
 
/*
$q = "select * from bab_baskets where ac=1";
$rs = mysql_query($q) or die(__LINE__.": ".mysql_error());

$bi='';

$i=0;
$z=0;
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)){

	//var_dump($row);


	$bo .= <<<EOT
	<fieldset><legend>{$row['nm']} ${$row['pr']} <input type="radio" name="o" value="1"></legend>
	<div class="points">${$row['pr']} pts</div>
	<img src="{$link}/bab/images/{$row['im']}">
	</fieldset>
EOT;


}
*/

//category dropdown

$dd = '<select onchange="change_category(this.value);">';

foreach($categories as $cat){
	$dd .='<option value="'.$cat['name'].'">'.$cat['cat_name'].'</option>';
	$cat_names[]=$cat['name'];
}

$js_cat_names = json_encode($cat_names);


$dd .= '</select>';

  ?>
<div style=" width:804px; float:right;"> 
<div align="center" style="width:790px;">
</div>

<fieldset style="width:628px; margin-bottom:4px; min-height:112px;" class="your_basket">
	<legend>Your Basket</legend>
	<div style="float:left; margin-left:6px; margin-right:6px;">
		<img id="basket_image" src="bab/images/34.95.jpg" height="90px;">
	</div>
	<div style="margin-left:10px;  font-size:9px;" class="bskt_items" id="bskt_items">
		
		
		
	</div>

</fieldset>

<div style="float:right; width:162px;" align="center">
	<div class="heading" align="center" style="margin-bottom:12px; margin-top:12px;">Step 3: Check Out</div>
	<img src="bab/check_out.png" onclick="check_form();" style="cursor:pointer;" ><br><br>
	<div style="font-size:14px;">Pts Remaining: <b><span id="pts_rem"><?php echo $basket_points; ?></span></b></div>
	<div id="status"></div>
</div>

</div>

<br style="clear:both;">
<div style="width:1000px;">
	<div style="float:left; width: 494px;">
		<div align="center" class="heading">
		Step 1: Pick Your Basket
		</div>		
		<?php echo $bo; ?>
	</div>
	<div style="float:right; width: 504px;">
	<div class="heading" align="center">Item View</div>
			<table>
			<tr>
			<td width="30%">
			<div style="float:left; width:240px;" align="center">
							<img src="" id="the_img" style="margin:2px;">
						</div>
			</td>
			<td width="70%">
						<div style="float:right; width:264px;">
							<div id="product_description" style="padding:0px 30px;font-size:14px;">
								
							</div>
							<div ><br>
								<span id="product_pts"></span>pts
							</div>
							<div>
								<img src="./bab/add_to_basket.png" onclick="add_to_basket(active_basket_item);" style="cursor:pointer;"><br>
								<img src="./bab/remove.png" onclick="remove_it(active_basket_item);" style="cursor:pointer;">
							</div>
						</div>
			</td></tr></table>
						
	</div>
	<div style="clear:both;"></div>
</div> <!--  headsup -->
	
	
</div>


<div style="width:1000px;">
		<div align="center" class="heading">
		Step 2: Pick Your Items <?php echo $dd; ?> <div style="background:#FFFFFF;
    border-radius: 5px;
    color: #000000;
    cursor: pointer;
    float: right;
    line-height: 10px;
    margin-right: 1px;
    margin-top: 1px;
    padding: 4px;" onclick="show_items_only();">view items fullscreen</div>
		</div>
		
		<!-- 
		<div style="float:left; border:1px dashed red; width:100px; font-size:16px; line-height:20px; padding:2px;">Categories</div> 
		<ul class="cat_ul" style="float:left;">
		<?php
		/*
		foreach($categories as $cat){
				?>
				 <li style="border:1px dashed red; font-size:16px; line-height:20px; text-align:center; padding:2px;"><?php echo $cat['cat_name']; ?></li>
				<?php 
			}
		*/
		?>
		</ul>
		 -->
		 
	</div>

</div> <!--  position: static -->




<div class="bab" style="">
<!--   <form action="./bab.php" method="POST"> -->

<div style="width:1000px; margin-top:360px;" id="item_list" class="item_list">
	<div >
		<!--  
		<div style=" float:left; text-align:center; width:500px;margin:auto; min-height:235px;" align="center">	
			<table border="0" cellspacing="0" cellpadding="0" height="260px;">
		  		<tr>
		    		<td>
						<div style="float:left; width:250px;" align="center">
							<img src="<?php echo $link.'bab/images/'.$row['im']; ?>" id="the_img" style="margin:8px;">
						</div>
					</td>
					<td>
						<div style="float:left; width:250px;">
							<div id="product_description" style="padding:0px 30px;font-size:16px;">
								<?php echo $row['ds']; ?>
							</div>
						</div>
					</td>
		  		</tr>
			</table>
			<div style="clear:left; float:left; text-align: center; width: 500px;">
				<?php echo $bo; ?>
			</div>
			
	    </div>
	    -->
	  
<?php


//	for($j=0;$j<=$pages;$j++) { 	    

$hide=0;

foreach($categories as $cat){
	//$class='class="hidden"';
	
	if(!$hide){
		$hide=1;
		$class=' class="hidden" ';
	}
		echo '<div id="cat_id_'.$cat['name'].'" style=" clear:both; overflow-y:scroll;" '.$class.'>';
	?>
	
	<?php 
	//for($j=0; $j<1; $j++){
	foreach($bi[$cat['name']] as $hmm){
		
		echo $hmm;

?>
	<!--  	<div  id="page_0" class="pg"> -->
		
			<?php
				
			
			//echo "<br><Br>";
			
			//$bi[$cat['name']]
			
			/*
			if($j!=0){

			?>
			style="display:none;"
			<?php 
			}
*/
			?>
		
		<!-- 
			
			<table class="tb" style="width:240px; float:left; margin-top:0px;" border=0 cellpadding=0 cellspacing=0>
		-->
		
		
				<?php 
				/*
				foreach($hmm as $hm){
					echo $hm;
				}
				*/
				//echo $bi[$cat['name']][$j][0];
				?>
			<!--  
			</table>		
			<table style="width:240px;margin-left:6px; float:left; margin-top:0px;" border=0 cellpadding=0 cellspacing=0>
			-->
				<?php 
				//echo $bi[$cat['name']][$j][1];
				?>
		<!-- 
			</table>
		-->
		<!--  </div> -->
<?php 
	}
	?>
	
	
	</div>
	<?php
}
?>	

	</div>
	
<?php 
/*
$q = "select * from bab_baskets where ac=1";
$rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
$bi='';
$i=0;
$z=0;
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)){
	//var_dump($row);
	$bo .= <<<EOT
	<fieldset><legend>{$row['nm']} ${$row['pr']} <input type="radio" name="o" value="1"></legend>
	<div class="points">${$row['pr']} pts</div>
	<img src="{$link}/bab/images/{$row['im']}">
	</fieldset>
EOT;
	}
*/
?>
<!--  
	<div style="text-align:center; margin:auto; float:right;" align="center">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<td align="center">
					<div align="center" style="margin:auto; width:488px;">
<?php //echo $bo; ?>
						<a href="">prev</a> | <a href="">next</a>

						<div align="left">
							<div style="border-bottom:1px solid; text-align:left; line-height:26px;">
								<h1>Your Basket</h1>
							</div>
							item x 2 ...... 6 pts<br>
							item x 2 ...... 6 pts<br>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	-->
</div>
<!--  <input type="submit">  
</form> -->

<div style=" margin-top:40px; clear:both; display:none;">
<input type="text" name="cart_quantity" value="1" maxlength="6" size="4" />
<input type="hidden" value="180" name="products_id" id="products_id">
<input type="hidden" value="1" name="byob">
<input type="submit">

</div>

</form>

</div>



</div>
<script>
$(document).ready(function(){

checking_out=0;
	
active_basket_id='<?php echo $active_basket_id; ?>';
	
page=0;

$('#next').click(function(){
			pg_next();
		});
	$('#prev').click(function(){
		pg_prev();
	});
	
	pg_next = function(){
		$('#page_'+page).hide();
		page++;
		$('#page_'+page).show();	
	}

	pg_prev = function(){
		$('#page_'+page).hide();
		page--;
		$('#page_'+page).show();	
	}

	pts_total=0;
	
	change_basket = function(b){
	    
	    if(b.pt-pts_total < 0){
	       alert("You have too many items in your current basket to select this size.  Please remove " + Math.abs(b.pt-pts_total)+ " pts to select this basket ");
	       return false;   
	    }
		$('#basket_name').html(b.nm);
		$('#basket_price').html(b.pr);
		$('#pts_rem').html(b.pt-pts_total);

		pts_rem = b.pt-pts_total;

		$('#basket_image').attr({'src':'bab/images/'+(b.im)});
		$('#bsk_'+active_basket_id).toggleClass('active');
		$('#bsk_'+b.id).toggleClass('active');
		
		$('#products_id').val(basket_ids[b.id]);
		
		active_basket_id = b.id;
		
	};

	change_category = function(cat_id){
		for(i=0;i<cat_names.length;i++){
			$('#cat_id_'+cat_names[i]).attr({'class':'hidden'});
		}
		$('#cat_id_'+cat_id).attr({'class':''});
		resize_item_window();
		
	}
	
	add_to_basket = function(b_i){

		a_pts_rem = Number($('#pts_rem').html());
		
		a_pts_rem = a_pts_rem - Number(b_i.pt);

		if(a_pts_rem < 0){
			alert('Not enough points remaining.\n\nPlease select a larger basket to add this item,\nor contact us for assistance.');
			return false;
		}
		
		var nm = $.trim(b_i.nm.substring(0,11))+'...';
		
		if($.inArray(b_i.id, my_basket_items)>-1){

			if($.inArray(b_i.id, qtys)>-1){
				qtys[b_i.id]+=1;
			} else {
				qtys.push(b_i.id);
				qtys[b_i.id]=2;
			}
			$('#item_'+b_i.id).val(qtys[b_i.id]);
			// $('#in_basket_txt_'+b_i.id).html(nm+' x '+qtys[b_i.id]); 
		} else {

		my_basket_items.push(b_i.id);
		
		var dv = document.createElement('div');
		dv.id = 'in_basket_'+b_i.id;
		var im = document.createElement('img');

		var inp = document.createElement('input');

		inp.readOnly=true;
		
		inp.name='item_'+b_i.id;
		
		inp.id='item_'+b_i.id;
		
		inp.value=1;
		
		var spn = document.createElement('span');
		spn.id = 'in_basket_txt_'+b_i.id;
		var txt = document.createTextNode(nm);
		im.src='bab/images/'+b_i.im;

		im.style.cursor='pointer';

		im.onclick=function(){
			document.getElementById('the_img').src='./bab/images/'+b_i.im.replace('sm.jpg','jpg'); 
			document.getElementById('product_description').innerHTML=b_i.ds; 
			document.getElementById('product_pts').innerHTML=b_i.pt;
			active_basket_item = b_i;
		}
		
		dv.appendChild(inp);
		
		dv.appendChild(im);
		
		spn.appendChild(txt);

		br = document.createElement('br');
		br.style.clear='left';
		dv.appendChild(br);
		
		dv.appendChild(spn);
		
		
		
		document.getElementById('bskt_items').appendChild(dv);
		}
		
		pts_rem = Number($('#pts_rem').html());

		pts_total+=Number(b_i.pt);
		
		pts_rem = pts_rem - Number(b_i.pt);

		$('#pts_rem').html(pts_rem);
		
	};

	remove_it = function(b_i){
		
		if($.inArray(b_i.id, my_basket_items)>-1){
	
			err(b_i.id+ ' in basket');
			
			if($.inArray(b_i.id, qtys)>-1){

				err(b_i.id+ ' in qtys array');
				
				qtys[b_i.id]-=1;
				$('#item_'+b_i.id).val(qtys[b_i.id]);

				if($('#item_'+b_i.id).val()==0){
					$('#in_basket_'+b_i.id).remove();
					delete my_basket_items[my_basket_items.indexOf(b_i.id)];
					delete qtys[qtys.indexOf(b_i.id)];
				}

				pts_total-=Number(b_i.pt);
				
				pts_rem = pts_rem + Number(b_i.pt);

				$('#pts_rem').html(pts_rem);
				
			} else {

				err(b_i.id+ ' NOT in qtys array');
				
				pts_total-=Number(b_i.pt);
				$('#in_basket_'+b_i.id).remove();
				delete my_basket_items[my_basket_items.indexOf(b_i.id)];
				
				pts_rem = pts_rem + Number(b_i.pt);

				$('#pts_rem').html(pts_rem);
				
			}
			
		}
	}
	
	basket = [];
	basket_items = [];
	basket_ids = [];
	qtys = [];
	my_basket_items = [];
	
	active_cat_id = '<?php echo $active_cat_id; ?>';

	cat_names = eval(<?php echo $js_cat_names; ?>);

	first_id = '<?php echo $first_id; ?>';
	
	<?php 
	echo $js;
	echo $js2;
	
	echo $js_basket_ids;
	
	?>

	document.getElementById('the_img').src='./bab/images/'+basket_items[first_id]['im'].replace('sm.jpg','jpg'); 
	document.getElementById('product_description').innerHTML=basket_items[first_id]['ds']; 
	document.getElementById('product_pts').innerHTML=basket_items[first_id]['pt'];
	active_basket_item = basket_items[first_id];

	
	fullscreen=0;
	
	show_items_only = function(){
		if(!fullscreen){
			$('#headsup').attr({'class':'hidden'});
			
			$('#item_list').attr({'style':'margin-top:20px;'});
			fullscreen=1;
		} else {
			$('#headsup').attr({'class':''});

			$('#item_list').attr({'style':'margin-top:345px;'});
			fullscreen=0;
		}

		resize_item_window();
		
	}


	get_quad = function(pt){
		if(pt.pageX > $(window).width()/2){

			if((pt.pageY-$(window).scrollTop()) > $(window).height()/2){
				q = 'br';
			} else {
				q = 'ur';
			}
			
		} else {

			if((pt.pageY-$(window).scrollTop()) > $(window).height()/2){
				q = 'bl';
			} else {
				q = 'ul';
			}
			
		}
		
	}

	$('.basket_item_image').mousemove(function(e){
	      //alert(e.pageX+','+(e.pageY-$(window).scrollTop()));
			get_quad(e);
			pX=e.pageX;
			pY=e.pageY;
			if(q=='bl'){
				pY-=500;
				pX+=20;
			}
			if(q=='br'){
				pX-=500;
				pY-=500;
			}
			if(q=='ul'){
				pY+=20;
				pX+=20;
			}
			if(q=='ur'){
				pY+=20;
				pX-=500;
			}
			
			$('#img_container').attr({'style':'top:'+pY+'px; left:'+pX+'px; position:absolute;z-index:1'});
	}); 

var hit_checkout = 0;

	check_form = function(){
        
		checking_out=1;
		
		pts_rem = Number($('#pts_rem').html());
		
		if(pts_rem==0){

			document.forms[0].submit();
		
		} else {

			x = confirm('You have points remaining in your basket.  Are you sure you want to check out?');

			if(x){
				document.forms[0].submit();
			} else {
				return false;
				checking_out = 0;
				
			}
				
		}
		
		
		
	}
	
	err = function(m){
	    
		$('#err').html($('#err').html()+'<br>'+m);
	}

	resize_item_window = function(){

		if(fullscreen){
			ht=0;
		} else {
			ht = $('#headsup').height();
		}
		w_ht = $(window).height();
		p_ht = w_ht-ht-50;

		for(i=0;i<cat_names.length;i++){
			$('#cat_id_'+cat_names[i]).attr({'style':'height:'+p_ht+'px; overflow-y:scroll;'});
		}
		//$('#cat_id_'+cat_id).attr({'class':''});

	}

	change_category(cat_names[0]);

	window.onbeforeunload = before;
	
	
function before(evt)
{
   if(!checking_out){
   return "Closing this window will empty your basket.  OK?";
   }
   //If the return statement was not here, other code could be executed silently (with no pop-up)
}
	
});

</script>



</div>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//anythinginabasket.com/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//anythinginabasket.com/piwik/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
<?php
/*
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://anythinginabasket.com/metrics/" : "http://anythinginabasket.com/metrics/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) { alert(err);}
</script><noscript><p><img src="http://anythinginabasket.com/metrics/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
*/ 
?>
