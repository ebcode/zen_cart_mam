<?php

ini_set('display_errors','on');

if(!isset($_GET['debug'])){
//die('<div align="center">temporarily down for maintenance. please check back in a couple hours.</div>');
}
/**
 * index.php represents the hub of the Zen Cart MVC system
 * 
 * Overview of flow
 * <ul>
 * <li>Load application_top.php - see {@tutorial initsystem}</li>
 * <li>Set main language directory based on $_SESSION['language']</li>
 * <li>Load all *header_php.php files from includes/modules/pages/PAGE_NAME/</li>
 * <li>Load html_header.php (this is a common template file)</li>
 * <li>Load main_template_vars.php (this is a common template file)</li>
 * <li>Load on_load scripts (page based and site wide)</li>
 * <li>Load tpl_main_page.php (this is a common template file)</li>
 * <li>Load application_bottom.php</li>
 * </ul>
 *
 * @package general
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: index.php 2942 2006-02-02 04:41:23Z drbyte $
 */
/**
 * Load common library stuff 
 */
  require('includes/application_top.php');

  //require('naive_count.php');  //newsletter link tracking

  $language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/';
  $directory_array = $template->get_template_part($code_page_directory, '/^header_php/');
  foreach ($directory_array as $value) { 
/**
 * We now load header code for a given page. 
 * Page code is stored in includes/modules/pages/PAGE_NAME/directory 
 * 'header_php.php' files in that directory are loaded now.
 */
    require($code_page_directory . '/' . $value);
  }
/**
 * We now load the html_header.php file. This file contains code that would appear within the HTML <head></head> code 
 * it is overridable on a template and page basis. 
 * In that a custom template can define its own common/html_header.php file 
 */
  require($template->get_template_dir('html_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/html_header.php');
/**
 * Define Template Variables picked up from includes/main_template_vars.php unless a file exists in the
 * includes/pages/{page_name}/directory to overide. Allowing different pages to have different overall
 * templates.
 */
  require($template->get_template_dir('main_template_vars.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/main_template_vars.php');
/**
 * Read the "on_load" scripts for the individual page, and from the site-wide template settings
 * NOTE: on_load_*.js files must contain just the raw code to be inserted in the <body> tag in the on_load="" parameter.
 * Looking in "/includes/modules/pages" for files named "on_load_*.js"
 */
  $directory_array = $template->get_template_part(DIR_WS_MODULES . 'pages/' . $current_page_base, '/^on_load_/', '.js');
  foreach ($directory_array as $value) { 
    $onload_file = DIR_WS_MODULES . 'pages/' . $current_page_base . '/' . $value;
    $read_contents='';
    $lines = @file($onload_file);
    foreach($lines as $line) {
      $read_contents .= $line;
    }
  $za_onload_array[] = $read_contents;
  }
/**
 * now read "includes/templates/TEMPLATE/jscript/on_load/on_load_*.js", which would be site-wide settings
 */
  $directory_array=array();
  $tpl_dir=$template->get_template_dir('.js', DIR_WS_TEMPLATE, 'jscript/on_load', 'jscript/on_load_');
  $directory_array = $template->get_template_part($tpl_dir ,'/^on_load_/', '.js');
  foreach ($directory_array as $value) { 
    $onload_file = $tpl_dir . '/' . $value;
    $read_contents='';
    $lines = @file($onload_file);
    foreach($lines as $line) {
      $read_contents .= $line;
    }
    $za_onload_array[] = $read_contents;
  }

  // set $zc_first_field for backwards compatibility with previous version usage of this var
  if (isset($zc_first_field) && $zc_first_field !='') $za_onload_array[] = $zc_first_field;

  $zv_onload = "";
  if (isset($za_onload_array) && count($za_onload_array)>0) $zv_onload=implode(';',$za_onload_array);

  //ensure we have just one ';' between each, and at the end
  $zv_onload = str_replace(';;',';',$zv_onload.';');

  // ensure that a blank list is truly blank and thus ignored.
  if (trim($zv_onload) == ';') $zv_onload='';
/**
 * Define the template that will govern the overall page layout, can be done on a page by page basis
 * or using a default template. The default template installed will be a standard 3 column layout. This
 * template also loads the page body code based on the variable $body_code.
 */
  
  $link = HTTPS_SERVER . DIR_WS_CATALOG;
	
  
  if(isset($_GET['action'])){
  	switch($_GET['action']){
  		case 'empty':
  			unset($_SESSION['byob_id']);
  			unset($_SESSION['byob']);
			$_SESSION['cart']->reset();
  		break;
  		case 'instructions':
  			require($_SERVER['DOCUMENT_ROOT'].'/gift_baskets/includes/languages/english/html_includes/bookshelf/define_page_4.php');
  			die();
  			break;
  	}
  }
  
	if(count($_POST)){	
  	//get items with values
  	
		$q = "insert into bab_orders (basket_id) values ('1')";
		 
		mysql_query($q) or die(__LINE__.':'.mysql_errror());
		 
		$insert_id = mysql_insert_id();
		 
		$q = "insert into bab_order_items (bab_order_id, item_id, quantity) values ";
		
  		foreach($_POST as $k => $v){
  		
  			$x = substr($k, 0, 5);
  		
  			echo "<br>x=$x";	
  			
  			if($x=='item_'){
  				if((int)$v>0){ //quantity > 0
  					$item_id = explode('_',$k);
  					$item_id = $item_id[1];
  					echo "<br> ordering item #: ".$item_id;
  					$quantity = (int)$v;
  					
  					$q .= "('$insert_id', '$item_id', '$quantity'),";
  					
  				}
	  		}
	  	}
		
	  	echo "<br><br>q = $q";
	  	
	  	$q = substr($q, 0, strlen($q)-1); //trailing comma
	  	
	  	mysql_query($q) or die(__LINE__.": ".mysql_error());
	  	
	  	
	//die('OK');
	}
  
//detect $_SESSION['byob'] and offer to empty basket or redirect to checkout

	//var_dump ($_SESSION);

if(isset($_SESSION['byob_id'])){
	?>
	<div style="width:1000px; margin:auto; margin-top:20px;">
	<h2>It looks like you already have a Build Your Own Basket in your cart,  
	and only one basket can be processed at this time. <br><br>
	Would you like to empty this basket and start over, 
	or proceed to check out?
	<br><br>
	<a href="./byob.php?action=empty">Empty this basket.</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="./index.php?main_page=shopping_cart">Proceed to check out.</a><br><br>
	
	The following items are in your basket:<br>
	
	<?php 
	
	$byob_items = $db->Execute("select bo.quantity, bo.item_id, bi.* from bab_order_items bo left join bab_items bi on bo.item_id = bi.id where bo.bab_order_id = '".$_SESSION['byob_id']."'");
	
	while (!$byob_items->EOF) {
	
		/*
		$this->products[$index]['byob'][$subindex] = array('item_id' =>$byob_items->fields['item_id'],
				'quantity' =>$byob_items->fields['quantity'],
				'item_name' =>$byob_items->fields['nm']);
		$subindex++;
		*/
		echo "<br>".$byob_items->fields['nm']." .......... x ". $byob_items->fields['quantity'];
		$byob_items->MoveNext();
	}
	
	
} else {

	require($template->get_template_dir('tpl_byob.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_byob2.php');

}
 
  
?>
</body>
</html>
<?php
/**
 * Load general code run before page closes
 */
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
