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
  
  $link = HTTP_SERVER . DIR_WS_CATALOG;

$q = "select max(contest_id) as contest_id from contests";
    $rs = mysql_query($q);  
    $row = mysql_fetch_array($rs);
    
    $contest_id = $row['contest_id'];
  
//get number of entries in contest_entries
$q = "select count(*) as count from contest_entries where contest_id = '$contest_id`'";
$rs = mysql_query($q);  
$row = mysql_fetch_array($rs);

$contest_entries_count = $row['count'];
//echo "<br>count = $contest_entries_count";
    

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
  	 if(isset($_SESSION['customer_id'])){
        //check if they've already submitted an entry        
        $q = "select count(*) as count from contest_entries where customers_id = '".$_SESSION['customer_id']."' and contest_id = '".$contest_id."'";
        
        $rs = mysql_query($q);  
        $row = mysql_fetch_array($rs);
        
        $count = $row['count'];
        
        if($count){
            $contest_msg = "You have already entered this contest.";
        } else {
            
            //check if someone else has entered this guess
            //check if they've already submitted an entry        
        $q = "select count(*) as count from contest_entries where entry = '".mysql_real_escape_string($_POST['guess'])."' and contest_id = '".$contest_id."'";
        
        $rs = mysql_query($q);  
        $row = mysql_fetch_array($rs);
        
        $count = $row['count'];
             
        if($count){
            $contest_msg = "Another person already guessed <b>{$_POST['guess']}</b>!  Please try again.";
        } else {
             //insert entry into table
             $q = "insert into contest_entries (customers_id, entry, contest_id) values ('".$_SESSION['customer_id']."','".mysql_real_escape_string($_POST['guess'])."', '".$contest_id."')";
            //echo "<br>q = $q";     
            mysql_query($q) or die(mysql_error()); 
              $contest_msg = "Your guess has been added to the contest!";
        }
        }

    } else {
        $contest_msg = "You must be logged in to enter the contest.  <a href=\"index.php?main_page=login\">Log in or create an account here.</a>";
    }
    
    
	  	
	//die('OK');
	}
    
    if(isset($_SESSION['customer_id'])){
 } else {
        $contest_msg = "You must be logged in to enter the contest.  <a href=\"index.php?main_page=login\">Log in or create an account here.</a>";
    }
    //get all contest entries and names
    
    $q = "select entry, customers_firstname, customers_lastname from contest_entries ce left join customers c on ce.customers_id = c.customers_id";
    
    $rs = mysql_query($q);  
    
    while($rows[] = mysql_fetch_array($rs)){}

    foreach($rows as $v){
        if($v){
        //echo "<br> {$v['customers_firstname']}, entry: {$v['entry']}";
        }
    }
    //echo "contest_id = $contest_id";
    
//detect $_SESSION['byob'] and offer to empty basket or redirect to checkout

	//var_dump ($_SESSION);


	require($template->get_template_dir('tpl_contest.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_contest.php');
  


?>
</body>
</html>
<?php
/**
 * Load general code run before page closes
 */
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>