<?php
/**
 * Common Template - tpl_footer.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_footer.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_footer = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_footer.php 3183 2006-03-14 07:58:59Z birdbrain $
 */
require(DIR_WS_MODULES . zen_get_module_directory('footer.php'));
?>

<?php
if (!$flag_disable_footer) {
?>

<!--bof-navigation display -->
<?php /* 
<div id="navSuppWrapper">
<div id="navSupp">
<ul>
<!--<li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li>-->
<?php if (EZPAGES_STATUS_FOOTER == '1' or (EZPAGES_STATUS_FOOTER == '2' and (strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR'])))) { ?>
<li><?php require($template->get_template_dir('tpl_ezpages_bar_footer.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_ezpages_bar_footer.php'); ?></li>
<?php } ?>
</ul>
</div>
</div>
*/ ?>
<style>
    .the_footer{
        background-color:#43201c; color: #fff; width:1000px; border-radius:15px; position:relative; margin-top:10px;
    }
    .the_footer tr td a{
        color:#fff;
        font-size: 14px;
        line-height: 18px;
        text-decoration: none;
    }
    .the_categories{
    	background-color:#fff;
    	border-radius:10px;
    }
    .the_footer tr td a:hover{
        text-decoration:underline;   
    }
    .the_categories tr td a{
        color:#43201c;
    }
    .phone{
    font-weight:normal;
    font-size:20px;
    }
    .brown{
	color: #43201c;
	}
     .bold{
	font-weight:bold;
	}
	.footer a{
	text-decoration:none;
	}
	.footer a:hover{
	text-decoration:underline;
	}
</style>


<img alt="" height="20" src="images/New_Everyday._copy_copy.png?1455642157355" style="" width="1000">

<div style="position:relative; overflow:visible;">
<table width="1000px"
cellpadding="3" cellspacing="0" border="0" class="footer"
style=" left:0px; overflow:visible; height:100px; float:left;">
<tr>
    <td><a href="https://anythinginabasket.com/">Home</a></td>
    <td>
    <a href="https://anythinginabasket.com/index.php?main_page=page&id=3">About Us</a>
    </td>
    <td colspan="5" rowspan="2" align="right" class="phone"><a style="color:blue;" 
	href="./index.php?main_page=create_account">Subscribe to our newsletter for coupons, raffles and fun contests!</a>
    </td>
</tr>
<tr>
    <td><a href="https://anythinginabasket.com/index.php?main_page=account">My Account</a></td>
    <td><a href="https://anythinginabasket.com/index.php?main_page=shippinginfo">Shipping &amp; Delivery Info</a></td>
</tr>
<tr>
    <td><a href="https://anythinginabasket.com/index.php?main_page=shopping_cart">Shopping Cart</a></td>
    <td><a href="https://anythinginabasket.com/index.php?main_page=page&id=15">Lisa's Bio</a></td>
    <td colspan="2" rowspan="5" align="center" width="20%">
<!-- (c) 2005, 2012. Authorize.Net is a registered trademark of CyberSource Corporation -->
<div class="AuthorizeNetSeal" >
<script type="text/javascript" language="javascript">var ANS_customer_id="522f93f3-9d63-4c66-adba-3291d76efaa0";</script> 
<script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script> <br>
<a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Online Payment Processing</a>
    </td>
    
    </td>
    <td colspan="2" rowspan="5" align="center"><img src="/images/comodo_secure_100x85_transp.png">  </td>
    <td><span class="brown bold" >We Carry</span></td>
</tr>
<tr>
    <td><a href="https://anythinginabasket.com/index.php?main_page=contact_us">Contact Us</a></td>
    <td><a href="https://anythinginabasket.com/index.php?main_page=privacy">Privacy Policy</a></td>
    <td>Stonewall Kitchen Gourmet Foods</td>
</tr>
<tr>
    <td><a href="https://anythinginabasket.com/index.php?main_page=page&id=16">Our Guarantee</a></td>
    <td></td>
    <td>Brendt & Sam's Natural Cookies</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td>Lindt Chocolate</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td>Davidson's Organic Teas</td>
</tr>
<tr>
    <td align="center" valign="top" colspan="6" rowspan="3"><h2 class="brown">

<a href="http://www.facebook.com/pages/A-Tisket-A-Tasket-Anything-In-A-Basket-Inc/559571864068436" 
target="_blank" style="display:inline;"><img src="./images/facebook.png" width="32" height="32" style="margin:0 3px;" /></a>
<a href="https://plus.google.com/100741796007988148581" rel="publisher" style="display:inline;" target="_blank">
<img src="./images/gplus2.png" width="32" height="32" style="margin:0 3px;" target="_blank" /></a>
<a href="http://www.pinterest.com/anythinginabask/pins/" style="display:inline;" target="_blank">
<img src="./images/pinterest.png" width="32" height="32" style="margin:0 3px;" /></a>
<br>
How can we help you? Call us at 631.385.0001</h2></td>
    <td>21st Century Confections</td>
</tr>
<tr>
    <td>Dibella Buscotti</td>
</tr>
<tr>
    <td>Limited Ingredient Foods & More</td>
</tr>
</table>

</div>



<br style="clear:both">
<!--
<table border="0" cellpadding="5" cellspacing="5" style="" class="the_footer">
     <tr>

         <td width="25%" style="text-align:center;">
             <a href="https://anythinginabasket.com/">Home</a>&nbsp;&nbsp;
             <a href="https://anythinginabasket.com/index.php?main_page=account">My Account</a>&nbsp;&nbsp;
             <a href="https://anythinginabasket.com/index.php?main_page=shopping_cart">Shopping Cart</a>&nbsp;&nbsp;
             <a href="https://anythinginabasket.com/index.php?main_page=contact_us">Contact Us</a>&nbsp;&nbsp;

-->

             <!--<a href="https://anythinginabasket.com/index.php?main_page=page&id=17">Link Exchange</a>&nbsp;&nbsp;-->
 
<!--
             <a href="https://anythinginabasket.com/index.php?main_page=privacy">Privacy Policy</a>&nbsp;&nbsp;
             <a href="https://anythinginabasket.com/index.php?main_page=page&id=16">Our Guarantee</a>&nbsp;&nbsp;
             <a href="https://anythinginabasket.com/index.php?main_page=page&id=3">About Us</a>&nbsp;&nbsp;
-->

<!--             <a href="https://anythinginabasket.com/index.php?main_page=page&id=8">Corporate Orders</a>&nbsp;&nbsp; -->

<!--

             <a href="https://anythinginabasket.com/index.php?main_page=shippinginfo">Shipping &amp; Delivery Info</a>&nbsp;&nbsp;

-->
             <!--<a href="">Local Delivery Areas</a>&nbsp;&nbsp;-->



<!--
             <a href="https://anythinginabasket.com/index.php?main_page=page&id=15">Lisa's Bio</a>&nbsp;&nbsp;
-->

         <!-- </td>
         <td width="25%"> -->
             
<!--             
         </td>
-->
         <!--
         <td width="50%">
             <div align="center" style="font-size:16px; font-weight:bold; margin-bottom:5px">
                 Categories
             </div>
             <div style="background-color:#fff; color: #43201c; border-radius:10px;">
                 <table cellpadding="5" cellspacing="5" border="0" width="100%" class="the_categories">
                     <tr>
                         <td width="50%"><a href="https://anythinginabasket.com/index.php?main_page=index&cPath=36">Browse All Gift Baskets</a><br>
                             <a href="">Build Your Own Basket</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=index&cPath=28">All Holiday Gift Baskets</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=index&cPath=51">Candy Platters &amp; Bouquets</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=index&cPath=49">Cookie Bouquets</a><br>
                         </td>
                         <td><a href="https://anythinginabasket.com/index.php?main_page=index&cPath=48">Fresh Baked Cookies</a><br>
                             <a href="https://anythinginabasket.com/includes/templates/bookshelf/images/category_links/sweet_greetings.png">$9.95 Sweet Greetings</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=index&cPath=44">Party Favors &amp; Centerpieces</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=index&cPath=27">Shop By Occasion</a><br>
                             <a href="https://anythinginabasket.com/index.php?main_page=product_info&cPath=50&products_id=157">Graduation Gifts</a><br>
                         </td>
                     </tr>
                 </table>
             </div>
         </td> -->

<!--
     </tr>
 </table>
-->


<!--eof-navigation display -->

<!--bof-ip address display -->
<?php
//if (SHOW_FOOTER_IP == '1') {
if(0){
?>
<div id="siteinfoIP"><?php echo TEXT_YOUR_IP_ADDRESS . '  ' . $_SERVER['REMOTE_ADDR']; ?></div>
<?php

}
?>

<!--eof-ip address display -->

<!--bof-banner #5 display -->
<?php
  if (SHOW_BANNERS_GROUP_SET5 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET5)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerFive" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<!--eof-banner #5 display -->

<div align="center" style="margin-top:10px;">
<!--
<div style="position:relative; text-align:left; padding:15px;">
<a style="position:absolute; font-size:24px; color:blue; width:100%; text-align:center;" 
href="./index.php?main_page=create_account">Subscribe to our newsletter for coupons, raffles and fun contests!</a>
</div>
-->
</div>

<!--bof- site copyright display -->
<div id="siteinfoLegal" class="legalCopyright"><?php echo FOOTER_TEXT_BODY; ?></div>
<!--eof- site copyright display -->

<?php
} // flag_disable_footer
?>
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
