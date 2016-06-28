<?php

/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_categories.php 3157 2006-03-10 23:24:22Z drbyte $
 *
 * BetterCategoriesEzInfo v1.3.0.2 added  2006-06-22  gilby
 */

 
 /*
 
  $spacer = '';
  // uncomment next line to add 1 space between image & text
  // $spacer .= '&nbsp;';


  $content = "";
  
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";
    for ($i=0;$i<sizeof($box_categories_array);$i++) {
    switch(true) {
// to make a specific category stand out define a new class in the stylesheet example: A.category-holiday
// uncomment the select below and set the cPath=3 to the cPath= your_categories_id
// many variations of this can be done
//      case ($box_categories_array[$i]['path'] == 'cPath=3'):
//        $new_style = 'category-holiday';
//        break;
      case ($box_categories_array[$i]['top'] == 'true'):
        $new_style = 'category-top';
        break;
      case ($box_categories_array[$i]['has_sub_cat']):
        $new_style = 'category-subs';
        break;
      default:
        $new_style = 'category-products';
      }
     if (zen_get_product_types_to_category($box_categories_array[$i]['path']) == '3' or ($box_categories_array[$i]['top'] != 'true' and SHOW_CATEGORIES_SUBCATEGORIES_ALWAYS != 1)) {
        // skip it this is for the document box
      } else {
       $content .= '<div class="betterCategories"><a class="' . $new_style . '" href="' . zen_href_link(FILENAME_DEFAULT, $box_categories_array[$i]['path']) . '">';
	  if ($box_categories_array[$i]['current']) {
        if ($box_categories_array[$i]['has_sub_cat']) {
          $content .= '<span class="category-subs-parent">';
          $content .= cat_with_pointer($box_categories_array[$i]['name'], 'down', $spacer);
          $content .= '</span>';
        } else {
          $content .= '<span class="category-subs-selected">';
          $content .= cat_with_pointer($box_categories_array[$i]['name'], 'nosub', $spacer);
          $content .= '</span>';
        }
      } else {
        if ($box_categories_array[$i]['has_sub_cat']) { 
        $content .= cat_with_pointer($box_categories_array[$i]['name'], 'right', $spacer); }
        else { 
        $content .= cat_with_pointer($box_categories_array[$i]['name'], 'nosub', $spacer); }
      }

      if ($box_categories_array[$i]['has_sub_cat']) {
        $content .= CATEGORIES_SEPARATOR;
      }
      //$content .= '</a>';

      if (SHOW_COUNTS == 'true') {
        if ((CATEGORIES_COUNT_ZERO == '1' and $box_categories_array[$i]['count'] == 0) or $box_categories_array[$i]['count'] >= 1) {
          if ($current_page != 'index')
		  $content .= CATEGORIES_COUNT_PREFIX . $box_categories_array[$i]['count'] . CATEGORIES_COUNT_SUFFIX;
        }
      }

      $content .= '</a></div>';
    }
  }

  if (SHOW_CATEGORIES_BOX_SPECIALS == 'true' or SHOW_CATEGORIES_BOX_PRODUCTS_NEW == 'true' or SHOW_CATEGORIES_BOX_FEATURED_PRODUCTS == 'true' or SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
// display a separator between categories and links
    if (SHOW_CATEGORIES_SEPARATOR_LINK == '1') {
      //$content .= '<br />' . zen_draw_separator('pixel_silver.gif') . '<br />';
    }
    if (SHOW_CATEGORIES_BOX_SPECIALS == 'true') {
      $show_this = $db->Execute("select s.products_id from " . TABLE_SPECIALS . " s where s.status= '1' limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<div class="betterCategories"><a class="category-links" href="' . zen_href_link(FILENAME_SPECIALS) . '">';
       //  $content .= zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_boxes.gif') . $spacer;  -- elibird  
        $content .= CATEGORIES_BOX_HEADING_SPECIALS . '</a></div>';
      }
    }
    if (SHOW_CATEGORIES_BOX_PRODUCTS_NEW == 'true') {
      // display limits
      $display_limit = zen_get_products_new_timelimit();

      $show_this = $db->Execute("select p.products_id
                                 from " . TABLE_PRODUCTS . " p
                                 where p.products_status = '1' " . $display_limit . " limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<div class="betterCategories"><a class="category-links" href="' . zen_href_link(FILENAME_PRODUCTS_NEW) . '">';
       // $content .= zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_boxes.gif') . $spacer; --elibird 
        $content .= CATEGORIES_BOX_HEADING_WHATS_NEW . '</a></div>';
      }
    }
    if (SHOW_CATEGORIES_BOX_FEATURED_PRODUCTS == 'true') {
      $show_this = $db->Execute("select products_id from " . TABLE_FEATURED . " where status= '1' limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<div class="betterCategories"><a class="category-links" href="' . zen_href_link(FILENAME_FEATURED_PRODUCTS) . '">';
        // $content .= zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_boxes.gif') . $spacer; --elibird 
        $content .= CATEGORIES_BOX_HEADING_FEATURED_PRODUCTS . '</a></div>';
      }
    }
    if (SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
      $content .= '<div class="betterCategories"><a class="category-links" href="' . zen_href_link(FILENAME_PRODUCTS_ALL) . '">';
       //  $content .= zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_boxes.gif') . $spacer; --elibird 
      $content .= CATEGORIES_BOX_HEADING_PRODUCTS_ALL . '</a></div>';
    }
  }
  
  //this is the function that inserts the 'pointer' (or 'disclosure triangle')
  //before the name of the category, in the 'Categories' sidebox.
  //$categoryName should be a string as contained in 
  //$box_categories_array[$i]['name'] above, which already includes
  //the category name with all the necessary subcategory indents up front,
  //as specified by the user in the Admin Panel.
  //$categoryType will be either 'down', 'right' or 'nosub', which 
  //specifies which picture appears next to the category name:
  function cat_with_pointer( $categoryName, $categoryType, $spacer) {
  
  	//picking the appropriate pointer image:
  	switch ($categoryType) {
  		case "down":
  			$pointer = zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_pointer_down.gif');
  			break;
  		case "right":
  			$pointer = zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_pointer_right.gif');
  			break;
  		default:
  			//$pointer = zen_image(DIR_WS_TEMPLATE_IMAGES . 'bc_cat_no_sub.gif');
  			$pointer='';
  	}
  	
    $pointer .= $spacer;
  	
  	switch (true) {
  		//if the user has left the 'subcategories indent' empty, or
  		//if this is a 'top-level' category (there are no subcategory indents),
  		//then, we just prepend the pointer image:
  		case (CATEGORIES_SUBCATEGORIES_INDENT == ''):
  		case (strpos($categoryName, CATEGORIES_SUBCATEGORIES_INDENT) !== 0):
  			$pointer .= $categoryName;
  			break;
  		default:
  			//removing the subcategory indents from the beginning of the name:
  			$indentLength = strlen(CATEGORIES_SUBCATEGORIES_INDENT);
  			$pos = 0;
  			for ($i = 0; $pos === 0; $i++) {
  				$categoryName = substr($categoryName, $indentLength);
  				$pos = strpos($categoryName, CATEGORIES_SUBCATEGORIES_INDENT);
  			}
  			//placing the pointer image:
  			$pointer .= $categoryName;
  			//adding back the subcategory indents to the beginning of the name:
  			for (;$i > 0; $i--) {
  				$pointer = CATEGORIES_SUBCATEGORIES_INDENT . $pointer;
  			}
  	}
  	
  	return $pointer;
  
  }
  $content .= '</div>';
  * 
  */
?>
<!--
<div class="sideBoxContent" >
-->
<div class="hmm">
<!--
<div style="font-size:18px; font-weight:bold; margin-top:10px; margin-bottom:4px;" align="center">1.800.734.GIFT
</div>
<div style="font-size:18px; font-weight:bold; margin-top:8px; margin-bottom:6px;" align="center">631.385.0001
</div>
-->

    <a href="./index.php?main_page=index&cPath=36"><img src="./images/shop_all_baskets.png"></a>
    <!-- <a href="./index.php?main_page=index&cPath=28"><img src="./includes/templates/bookshelf/images/category_links/all_holiday_gift_baskets.png"></a> --> 
<!--
    <a style="float:right; 
    margin-right: 15px;" href="./index.php?main_page=index&cPath=28_30">
    <img src="./images/fall_and_thanksgiving.png" >
    <div style="position:absolute; display:inline;"><img src="./images/leaves.png" style="border:0; right:20px; bottom:4px; position:relative; "></div></a>
    <a style="float:right;  margin-bottom: 6px;
    margin-right: 15px;" href="./index.php?main_page=index&cPath=28_29">
    <img src="./images/christmas_holiday_button.png" >
    <div style="position:absolute; display:inline;">
    <img src="./images/wreath2.png" style="border:0;right:33px;bottom:-2px; position:relative;"></div></a> 
-->
    
<!--
    <a style="float:right; margin-right:20px;" href="./index.php?main_page=index&cPath=28_33">
<img src="./images/valentines_day_button.png">
<div style="position:absolute; display:inline;">
<img src="./images/valentines_heart.png" style="border:0; right:49px; bottom:6px; position:relative; ">
</div></a> 
    -->
<!--
    <a style="float:right; margin-right:20px;" href="./index.php?main_page=index&cPath=28_39">
<img src="./images/easter.png" >
<div style="position:absolute; display:inline;">
<img src="./images/easter_egg.png" style="border:0;height:36px;right:49px;bottom:4px;position:relative;">
</div></a>
-->
<!--
    <a style="float:right; margin-right:20px;" href="index.php?main_page=index&cPath=28_41">
<img src="./images/secretaries_day.png" ></a>
    <a style="float:right; margin-right:20px;" href="index.php?main_page=index&cPath=28_37">
<img src="./images/mothers_day.png" ></a> 
-->

    <a href="./byob.php" style="clear:right;"><img src="./includes/templates/bookshelf/images/category_links/build_your_own_basket.png"></a>

    <!--
    <a style="float:right; margin-right:20px;" href="index.php?main_page=product_info&cPath=36&products_id=66"><img src="./images/teacher_appreciation.png" ></a>
    -->
<!--    <a href="./index.php?main_page=index&cPath=51"><img src="./includes/templates/bookshelf/images/category_links/candy_platters_and_bouquets.png"></a> -->
<!--    <a href="./index.php?main_page=index&cPath=49"><img src="./includes/templates/bookshelf/images/category_links/cookie_bouquets.png"></a>
    <a href="./index.php?main_page=index&cPath=48"><img src="./includes/templates/bookshelf/images/category_links/fresh_baked_cookies.png"></a>  -->
    <!-- <div align="center" style="margin-top:10px; font-size:14px"><b>Coming Soon!</b></div>  -->
    
    
    <!-- <a href="./showroom.php"><img src="./images/showroom_button.png"></a> -->
    <a href="./gifts_under_25.php"><img src="./images/gifts_under_25_button.png"></a>
    <a href="./basket_specials.php"><img src="./images/gift_basket_specials.png"></a>

<!--    <a href="./index.php?main_page=index&cPath=52"><img src="./includes/templates/bookshelf/images/category_links/sweet_greetings.png"></a>
-->
<!--    <a href="./index.php?main_page=index&cPath=44"><img src="./includes/templates/bookshelf/images/category_links/party_favors_and_centerpiec.png"></a> -->
<!--    <a href="./index.php?main_page=index&cPath=27"><img src="./includes/templates/bookshelf/images/category_links/shop_by_occasion.png"></a>
<div align="center" > -->
<!--
<img src="./images/payment_icons.png" width="150" height="25" />
-->

<?php //require(DIR_WS_MODULES . 'sideboxes/search_header.php'); ?>

<?php 

include($_SERVER['DOCUMENT_ROOT']. DIR_WS_CATALOG .'includes/languages/english/html_includes/define_special_offer.php'); 

?>

<a href="https://www.anythinginabasket.com/images/zone_map_large.png" target="_blank">
<?php 

include($_SERVER['DOCUMENT_ROOT']. DIR_WS_CATALOG .'includes/languages/english/html_includes/define_page_5.php'); 

?>
</a>

<!--
    <a href="/blog"><img src="./images/anything_in_a_blog_new.png"></a>
    <a href="./contest.php"><img src="./images/contest_page_new.png"></a>
-->

<!--
<div align="center">
    <br>
<a href="./index.php?main_page=create_account">Subscribe to our newsletter for coupons, raffles and fun contests!</a>
<br>
</div>
-->
<!--
<div style="margin-top:8px; margin-bottom:4px; text-align:center;">
<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" target="_blank" style="display:inline;"><img src="./images/facebook.png" width="32" height="32" style="margin:0 3px;" /></a>
<a href="https://plus.google.com/100741796007988148581" rel="publisher" style="display:inline;" target="_blank"><img src="./images/gplus2.png" width="32" height="32" style="margin:0 3px;" target="_blank" /></a>

<?php
/*
<a href="https://twitter.com/AnythingInABask" style="display:inline;" target="_blank"><img src="./images/twitter.png" width="32" height="32" style="margin:0 3px;" /></a>
*/
?>

<a href="http://www.pinterest.com/anythinginabask/pins/" style="display:inline;" target="_blank"><img src="./images/pinterest.png" width="32" height="32" style="margin:0 3px;" /></a>
</div>
-->

<?php 

include($_SERVER['DOCUMENT_ROOT']. DIR_WS_CATALOG .'includes/languages/english/html_includes/left_side_text.php'); 

?>

<?php 
/*

<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" target="_blank" style="display:inline;"><img src="./images/facebook.png" width="32" height="32" style="margin:0 8px;" /></a>
<a href="https://plus.google.com/100741796007988148581" rel="publisher" style="display:inline;"><img src="./images/gplus2.png" width="32" height="32" style="margin:0 8px;" target="_blank" /></a>
<a href="https://twitter.com/AnythingInABask" style="display:inline;"><img src="./images/twitter.png" width="32" height="32" style="margin:0 8px;" /></a>

<!--
<div><br><img src="./images/PayPal_mark_60x38.gif" width="60" height="38" /></div>
<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" target="_blank">
<img src="./userfiles2/like_us_on_facebook.jpg"></a>
</div>  -->


<div align="center" style="clear:both;">
<div style="float:left; margin-bottom:5px;"><span class='st_facebook_hcount' displayText='Facebook'></span></div>
<div style="float:right; margin-bottom:5px;"><span class='st_googleplus_hcount' displayText='Google +'></span></div>
<div style="float:left; margin-bottom:5px"><span class='st_twitter_hcount' displayText='Tweet'></span></div>
<div style="float:right; margin-bottom:5px"><span class='st_pinterest_hcount' displayText='Pinterest'></span></div>

<div style="float:right; margin-bottom:6px;">
<a href="https://twitter.com/anythinginabask" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @anythinginabask</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div style="float:right" class="fb-like" data-href="https://www.facebook.com/ATisketATasketAnythingInABasket" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="box_count" data-action="like" data-show-faces="false" data-send="false"></div>

<script type="text/javascript">
					!function(doc, id){
  var js;
  var scriptElement = doc.getElementsByTagName("script")[0];
  if (!doc.getElementById(id)) {
    js = doc.createElement("script");
    js.id = id;
    js.src = "//dyn.yelpcdn.com/biz_badge_js/rrc/TriG92On9FH7Nz8PNae0vQ.js";
    scriptElement.parentNode.insertBefore(js, scriptElement);
  }
} (document, "yelp-biz-badge-script-rrc-TriG92On9FH7Nz8PNae0vQ");
</script>

</div>

<div id="yelp-biz-badge-rrc-TriG92On9FH7Nz8PNae0vQ">Anything In A Basket A Tisket A Tasket</div>

*/
 ?>




 
<?php /*
<!-- (c) 2005, 2012. Authorize.Net is a registered trademark of CyberSource Corporation -->
<div class="AuthorizeNetSeal" style="float:left; width:230px; margin-top:0px;"> <script type="text/javascript" language="javascript">var ANS_customer_id="522f93f3-9d63-4c66-adba-3291d76efaa0";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Online Payment Processing</a></div>
*/
?>


<div align="center">
<script type="text/javascript" data-pp-pubid="8500f2c32c" data-pp-placementtype="234x60">
/*
 (function (d, t) {
"use strict";
var s = d.getElementsByTagName(t)[0], n = d.createElement(t);
n.src = "//paypal.adtag.where.com/merchant.js";
s.parentNode.insertBefore(n, s);
}(document, "script"));
*/
</script>
</div>

<div id="fb-root" style="float:left;"></div>
<script>
/*
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
*/
</script>

<!--
<div style="border:0px dashed green; height:78px;">
<div style="float:left" class="fb-like" data-href="https://www.facebook.com/ATisketATasketAnythingInABasket" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>
<div style="margin-bottom:5px; float:left;"><span class='st_facebook_hcount' displayText='Facebook'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_googleplus_hcount' displayText='Google +'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_twitter_hcount' displayText='Tweet'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_pinterest_hcount' displayText='Pinterest'></span></div>
</div>
-->
</div>
</div>
