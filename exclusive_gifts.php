
<?php
error_reporting(E_ALL);
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
  
    
//detect $_SESSION['byob'] and offer to empty basket or redirect to checkout

    //var_dump ($_SESSION);
    $zz = 0;
    // function display_basket($row, $zz){
        //    
        
        
        //return $y;
    //}

    $products = '';
    
    $q = "select p.products_image, p.products_price, p.sold, p.products_quantity, pd.* from products p left join products_description pd on p.products_id = pd.products_id where p.master_categories_id = '55' order by products_sort_order";
    $rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
    
$table = '<tr>';

while ($row = mysql_fetch_array($rs)){
        
     //var_dump($row);
     $zz++;
     //$x = display_basket($row, $zz);
        
        if($zz%4==0){
            $y .= '<tr>';
        }   
        
        $price = number_format($row['products_price'], 2);

$tmp_image_file = DIR_WS_IMAGES . $row['products_image_2'];

$products_name = $row['products_name'];

$rel = 'lightbox';

$a = "<a style=\"cursor:default\">";

if($row['products_image_2']){
$a = "<a href=\"" . zen_lightbox($tmp_image_file, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . "\" rel=\"" . $rel . "\" title=\"" . ($products_name) . "\">";
}

        $y = "<td><form enctype=\"multipart/form-data\" method=\"post\" action=\"https://www.anythinginabasket.com/gift_baskets/index.php?main_page=product_info&cPath=36&products_id={$row['products_id']}&number_of_uploads=0&action=add_product\" name=\"cart_quantity\"><input type=\"hidden\" value=\"1\" name=\"cart_quantity\"><input type=\"hidden\" value=\"{$row['products_id']}\" name=\"products_id\"><div class=\"displayitem_container\" style=\"display:inline-block;\" >$a<div  class=\"displayitem\" id=\"item_{$row['products_id']}\" style=\"background: url('./images/{$row['products_image']}') no-repeat scroll center;\"><div class=\"price\">".'$'."{$price}</div><div class=\"hide_me\" id=\"item_{$row['products_id']}_description\"><br>{$row['products_description']}</div>";
        
        if($row['sold'] || $row['products_quantity'] == 0){
         $y .=       "<img src=\"./images/SOLD-1.png\" style=\"\">";
        }     
        //class=\"button\" //for button below
        $y .= "</div></div></a><div class=\"button\"><input type=\"submit\" style=\" cursor-events:none; display: none;\" value=\"add to cart\" id=\"button_item_{$row['products_id']}\"></div><div class=\"displayitem_title\">{$row['products_name']}</div></form></td>";
        
         if($zz%4==0){
            $y .= '</tr>';
        }   


     $products .= $y;
    }
   
    $products = '<tr>' . $products . '</tr>';

   require($template->get_template_dir('tpl_byob.php',
        DIR_WS_TEMPLATE, 
        $current_page_base,'templates'). '/tpl_exclusive_gifts.php');
    

  
?>
</body>
</html>
<?php
/**
 * Load general code run before page closes
 */
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>