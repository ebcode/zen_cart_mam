<?php
/**
 * Page Template
 *
 * Main index page<br />
 * Displays greetings, welcome text (define-page content), and various centerboxes depending on switch settings in Admin<br />
 * Centerboxes are called as necessary
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_index_default.php 3464 2006-04-19 00:07:26Z ajeh $
 */
?>
<div class="centerColumn" id="indexDefault">
<h1 id="indexDefaultHeading"><?php echo HEADING_TITLE; ?></h1>

<?php if (SHOW_CUSTOMER_GREETING == 1) { ?>
<h2 class="greeting"><?php echo zen_customer_greeting(); ?></h2>
<?php } ?>

<!-- deprecated - to use uncomment this section
<?php if (TEXT_MAIN) { ?>
<div id="" class="content"><?php echo TEXT_MAIN; ?></div>
<?php } ?>-->

<!-- deprecated - to use uncomment this section
<?php if (TEXT_INFORMATION) { ?>
<div id="" class="content"><?php echo TEXT_INFORMATION; ?></div>
<?php } ?>-->

<?php if (DEFINE_MAIN_PAGE_STATUS >= 1 and DEFINE_MAIN_PAGE_STATUS <= 2) { ?>
<?php
/**
 * get the Define Main Page Text
 */
?>
<div id="indexDefaultMainContent" class="content"><?php require($define_page); ?></div>
<?php } ?>

<!--
<div style="margin-top:6px; background-color: #43201C;
    border-radius: 15px;
    height: 62px;
    padding: 3px;
}">
<div style="float:left; margin-top:10px;">
<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" target="_blank" style="display:inline;"><img src="./images/facebook.png" width="32" height="32" style="margin:0 8px;" /></a>
<a href="https://plus.google.com/100741796007988148581" rel="publisher" style="display:inline;"><img src="./images/gplus2.png" width="32" height="32" style="margin:0 8px;" target="_blank" /></a>
<a href="https://twitter.com/AnythingInABask" style="display:inline;"><img src="./images/twitter.png" width="32" height="32" style="margin:0 8px;" /></a>
</div> -->
<!--
<div><br><img src="./images/PayPal_mark_60x38.gif" width="60" height="38" /></div>
<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" target="_blank">
<img src="./userfiles2/like_us_on_facebook.jpg"></a>
</div>  -->
<!--
<div style="float:left; width:208px; margin-top:5px;">

<div style="margin-bottom:5px; float:left;"><span class='st_facebook_hcount' displayText='Facebook'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_googleplus_hcount' displayText='Google +'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_twitter_hcount' displayText='Tweet'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_pinterest_hcount' displayText='Pinterest'></span></div>
</div>
<div style="float:left; margin-bottom:6px; margin-top:10px;">
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



<div id="yelp-biz-badge-rrc-TriG92On9FH7Nz8PNae0vQ" style="margin-left: 6px;">Anything In A Basket A Tisket A Tasket</div>

</div>
-->

<?php
  $show_display_category = $db->Execute(SQL_SHOW_PRODUCT_INFO_MAIN);
  while (!$show_display_category->EOF) {
?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_FEATURED_PRODUCTS') { ?>
<?php
/**
 * display the Featured Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_featured_products.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_featured_products.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_SPECIALS_PRODUCTS') { ?>
<?php
/**
 * display the Special Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_specials_default.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_NEW_PRODUCTS') { ?>
<?php
/**
 * display the New Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_UPCOMING') { ?>
<?php
/**
 * display the Upcoming Products Center Box
 */
?>
<?php include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_UPCOMING_PRODUCTS)); ?><?php } ?>


<?php
  $show_display_category->MoveNext();
} // !EOF
?>
</div>
