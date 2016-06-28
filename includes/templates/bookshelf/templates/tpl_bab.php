<?php

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
<script src="http://code.jquery.com/jquery-latest.js"></script>
<style>

table{

float:left;
}

/*
fieldset{
float:left;
width:200px;
}
*/

fieldset{
display:inline-block;
border:0;
font-size:11px;
margin:2px;
padding:0px;
}

legend {
   padding: 0 10px;
}

fieldset img{
height:162px;
margin-top:3px;
}

.checkbox{
width:20px;
height:20px;
border:1px solid #666;
margin:0px 20px;
background-color:#fff;
}

td{
text-align:center;
}


table img{
margin:2px;
}
#the_img{
max-height:200px;
max-width:200px;
/*
fieldset{
text-align:center;
line-height:8px;
padding:2px;
}
*/

.bg1{
background-color:#eee;	
}
.bg2{
background-color:#fff;
}
.points{
margin:10px auto;
}
/*
.bab fieldset{
width:24%;
margin:auto;
}
*/
</style>
<?php
  if (SHOW_BANNERS_GROUP_SET1 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET1)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerOne" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>

<div id="mainWrapper">
<?php
 /**
  * prepares and displays header output
  *
  */
  if (CUSTOMERS_APPROVAL_AUTHORIZATION == 1 && CUSTOMERS_AUTHORIZATION_HEADER_OFF == 'true' and ($_SESSION['customers_authorization'] != 0 or $_SESSION['customer_id'] == '')) {
    $flag_disable_header = true;
  }
  require($template->get_template_dir('tpl_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_header.php');?>

<!--  <table width="100%" border="0" cellspacing="0" cellpadding="0" id="contentMainWrapper">
  <tr>
  -->
  
  <?php 
  
  $q2 = "select * from bab_baskets where ac=1";
  $rs2 = mysql_query($q2) or die(__LINE__.": ".mysql_error());
  
  $bi='';
  
  $i=0;
  $z=0;
  
  $bo='';
  
  
  while($row2 = mysql_fetch_array($rs2, MYSQL_ASSOC)){
  
  	//var_dump($row);
  
  
  	$bo .= <<<EOT
<fieldset><legend><input type="radio" name="o" value="1"> {$row2['nm']} <br> &#36;{$row2['pr']} &ndash; {$row2['pt']} pts</legend><img src="{$link}/bab/images/{$row2['im']}"></fieldset>
EOT;
  
  
  }
  
  
  $q = "select * from bab_items where ac=1";
  $rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
  
  $bi='';
  
  $i=0;
  $z=0;
  while($row = mysql_fetch_array($rs, MYSQL_ASSOC)){
	
	//var_dump($row);
	
	$i+=1;
	
	$bi[$z] .= <<<EOT
<tr>
 		<td align="center">
 		
 		<img src="{$link}/bab/images/{$row['im']}" height="64px" align="middle" onclick="document.getElementById('the_img').src='{$link}/bab/images/{$row['im']}'; ;">
 		
 		</td>
	    
	    <td>{$row['nm']}<br>
	    {$row['pt']} pts
	    </td>
 		<!--
	    <td></td>
	    <td></td>
	    -->
	    <td><input size="2" style="width:20px" value="0"></td>
</tr>
EOT;
	if($i%4===0){
		$z+=1;
	}
	
}





mysql_data_seek($rs,0);
$row = mysql_fetch_array($rs, MYSQL_ASSOC);


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
  ?>
<div class="bab" style="border:0px dashed blue; height:478px;">
<div style="width:1000px;border:0px solid black; margin:auto;">
	<div >
		<div style=" float:left; text-align:center; width:500px;margin:auto; min-height:235px;" align="center">
			
		<table border="0" cellspacing="0" cellpadding="0" height="260px;">
		  <tr>
		  	<td colspan="2" valign="top"><div style="border-bottom:1px solid;min-height:26px;line-height:26px;"><h1>Build A Basket</h1></div></td>
		  </tr>
		  <tr>
		    <td>
						<div style="float:left; width:250px;" align="center">
						<img src="<?php echo $link.'bab/images/'.$row['im']; ?>" id="the_img" style="margin:8px;">
					</div>
		
			</td><td>
					
				
					
						<div style="float:left; width:250px;">
						
					
					<div id="product_description" style="padding:0px 30px;font-size:16px;">
							<?php echo $row['ds']; ?>
					</div>
			</div>
			</td>
		  </tr>
		</table>
	    </div>
	    <div style="border:0px dashed red;float:right;">
			<table class="tb" style="width:240px;border:0px dashed red; float:left; margin-top:30px;" border=0 cellpadding=0 cellspacing=0>
				<?php 
				echo $bi[0];
				?>
			</table>		
			<table style="width:240px;border:0px dashed red;margin-left:6px; float:left; margin-top:30px;" border=0 cellpadding=0 cellspacing=0>
				<?php 
				echo $bi[1];
				?>
			</table>
		</div>
	</div>
	<div style="clear:left; float:left;   text-align: center;
    width: 500px;">
	<?php echo $bo; ?>
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
<div style="text-align:center; margin:auto; float:right;" align="center">
<table border="0" cellspacing="0" cellpadding="0" width="100%" ><tr><td align="center">
<div align="center" style="margin:auto; width:488px;">
<?php //echo $bo; ?>
<a href="">prev</a> | <a href="">next</a>

<div align="left">
<div style="border-bottom:1px solid; text-align:left;"><h1>Your Basket:</h1></div>
item x 2 ...... 6 pts<br>
item x 2 ...... 6 pts<br>
</div>


</div>
</td></tr></table>
</div>



</div>
</div>