<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: tpl_drop_menu.php  2005/06/15 15:39:05 DrByte Exp $
//

?>
<!-- menu area -->
<div id="dropMenuWrapper">
  <div id="dropMenuWrapperb">
    <div id="dropMenuWrapperc">
      <div id="dropMenuWrapperd">
        <div id="dropMenu" >
          <ul class="level1">
 <!--           <li class="submenu"><a href="<?php echo zen_href_link(FILENAME_DEFAULT); ?>"><?php echo HEADER_TITLE_CATALOG; ?></a>
              <ul class="level2">
                <li><a href="<?php echo zen_href_link(FILENAME_PRODUCTS_NEW); ?>"><?php echo HEADER_TITLE_NEW_PRODUCTS; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_PRODUCTS_ALL); ?>"><?php echo HEADER_TITLE_ALL_PRODUCTS; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_SPECIALS); ?>"><?php echo HEADER_TITLE_SPECIALS; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_ADVANCED_SEARCH); ?>"><?php echo HEADER_TITLE_SEARCH; ?></a></li>
              </ul>
            </li>-->
            <li class="submenu"><a class="noLine" href="the-boutique-room-ezp-9.html?chapter=0">The Boutique Room</a></li>						
            <li class="submenu"><a href="<?php echo zen_href_link(FILENAME_SITE_MAP); ?>"><?php echo HEADER_TITLE_CATEGORIES; ?></a>
              <?php

 // load the UL-generator class and produce the menu list dynamically from there
 require_once (DIR_WS_CLASSES . 'categories_ul_generator.php');
 $zen_CategoriesUL = new zen_categories_ul_generator;
 $menulist = $zen_CategoriesUL->buildTree(true);
 $menulist = str_replace('"level4"','"level5"',$menulist);
 $menulist = str_replace('"level3"','"level4"',$menulist);
 $menulist = str_replace('"level2"','"level3"',$menulist);
 $menulist = str_replace('"level1"','"level2"',$menulist);
 //$menulist = str_replace('<li class="submenu">','<li class="submenu">',$menulist);
 $menulist = str_replace("</li>\n</ul>\n</li>\n</ul>\n","</li>\n</ul>\n",$menulist);
 echo $menulist;
?>
            </li>
<!--            <li class="submenu"><a href="<?php echo zen_href_link(FILENAME_DEFAULT); ?>"><?php echo HEADER_TITLE_INFORMATION; ?></a>
              <ul class="level2">
			    <?php if (DEFINE_SHIPPINGINFO_STATUS <= 1) { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_SHIPPING); ?>"><?php echo HEADER_TITLE_SHIPPING_INFO; ?></a></li>
				<?php } ?>
				<?php if (DEFINE_PRIVACY_STATUS <= 1)  { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_PRIVACY); ?>"><?php echo HEADER_TITLE_PRIVACY_POLICY; ?></a></li>
				<?php } ?>
				<?php if (DEFINE_CONDITIONS_STATUS <= 1) { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_CONDITIONS); ?>"><?php echo HEADER_TITLE_CONDITIONS_OF_USE; ?></a></li>
				<?php } ?>
                <li><a href="<?php echo zen_href_link(FILENAME_ABOUT_US); ?>"><?php echo HEADER_TITLE_ABOUT_US; ?></a></li>
                <?php if (DEFINE_SITE_MAP_STATUS <= 1) { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_SITE_MAP); ?>"><?php echo HEADER_TITLE_SITE_MAP; ?></a></li>
                <?php } ?>
                <?php if (MODULE_ORDER_TOTAL_GV_STATUS == 'true') { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_GV_FAQ, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_GV_FAQ; ?></a></li>
                <?php } ?>
                <?php if (MODULE_ORDER_TOTAL_COUPON_STATUS == 'true') { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_DISCOUNT_COUPON, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_DISCOUNT_COUPON; ?></a></li>
                <?php } ?>
                <?php if (SHOW_NEWSLETTER_UNSUBSCRIBE_LINK == 'true') { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_UNSUBSCRIBE, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_UNSUBSCRIBE; ?></a></li>
                <?php } ?>
                <?php require(DIR_WS_MODULES . 'sideboxes/' . $template_dir . '/' . 'ezpages_drop_menu.php'); ?>
              </ul>
            </li>
            <li><a href="<?php echo zen_href_link(FILENAME_CONTACT_US, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CONTACT_US; ?></a></li>
            <li class="submenu"><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a>
              <ul class="level2">
                <?php if ($_SESSION['customer_id']) { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL'); ?>"><?php echo HEADER_TITLE_NEWSLETTERS; ?></a></li>
                <?php } else { ?>
                <li><a href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>
                <li><a href="<?php echo zen_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_CREATE_ACCOUNT; ?></a></li>
                <?php } ?>
              </ul>
            </li>
-->			
            <li class="submenu"><a class="noLine" href="specialty-foods-ezp-11.html?chapter=0">Specialty Foods</a></li>
            <li class="submenu"><a class="noLine" href="corporate-ezp-8.html?chapter=0">Corporate</a>	
              <ul class="level2">
                <li><a href="corporate-ezp-8.html?chapter=0#house-application">House Account Application</a></li>
                <li><a href="corporate-ezp-8.html?chapter=0#corprate-disc">Corporate Discounts</a></li>
                <li><a href="corporate-ezp-8.html?chapter=0#large-orders">Large Corporate Orders</a></li>
              </ul>		
			</li>
			<li class="submenu"><a class="noLine" href="corporate-ezp-8.html?chapter=0">Delivery Info</a>	
              <ul class="level2">
                <li><a href="shippinginfo.html#guarantee">Guarantee</a></li>
                <li><a href="shippinginfo.html#correct-address">Correct Delivery Address</a></li>
                <li><a href="shippinginfo.html##refund-policy">Refund Policy</a></li>
                <li><a href="shippinginfo.html#perishables">Delivering Perishables</a></li>
                <li><a href="shippinginfo.html#sales-tax">Sales Tax</a></li>
                <li><a href="shippinginfo.html#shipping-rates">Shipping Rates</a></li>
                <li><a href="shippinginfo.html#local">Local/NY Delivery Rates/Info</a></li>
                <li><a href="shippinginfo.html#ship-info">Shipping &amp; Delivery Info</a></li>				
                <li><a href="shippinginfo.html#track-order">To Track an Order</a></li>
                <li><a href="shippinginfo.html#hospitals">Hospital Deliveries</a></li>
                <li><a href="shippinginfo.html#holiday">Holiday Shipping/Delivering</a></li>				
                <li><a href="shippinginfo.html#additional-charges">Additional Del/Ship Charges</a></li>
                <li><a href="shippinginfo.html#multiple">Multiples/Same Address</a></li>
              </ul>		
			</li>
            <?php if ($_SESSION['cart']->count_contents() != 0) { ?>
            <li class="submenu"><a class="noLine" href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a>
              <ul class="level2">
                <li><a href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>
              </ul>
            </li>
            <?php } else { ?>
            <li class="submenu"s><a class="noLine" href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a></li>
            <?php } ?>
            <li class="submenu"><a class="noLine" href="./index.php?main_page=page&id=3&chapter=0">About</a></li>
            
          </ul>
        </div>
      </div>
    </div>
  </div>
</div><!-- end dropMenuWrapper-->
<div class="clearBoth"></div>