<?php

/**

 * Common Template - tpl_header.php

 *

 * this file can be copied to /templates/your_template_dir/pagename<br />

 * example: to override the privacy page<br />

 * make a directory /templates/my_template/privacy<br />

 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_header.php<br />

 * to override the global settings and turn off the footer un-comment the following line:<br />

 * <br />

 * $flag_disable_header = true;<br />

 *

 * @package templateSystem

 * @copyright Copyright 2003-2006 Zen Cart Development Team

 * @copyright Portions Copyright 2003 osCommerce

 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

 * @version $Id: tpl_header.php 4813 2006-10-23 02:13:53Z drbyte $

 */

?>



<?php

  // Display all header alerts via messageStack:

  if ($messageStack->size('header') > 0) {

    echo $messageStack->output('header');

  }

  if (isset($_GET['error_message']) && zen_not_null($_GET['error_message'])) {

  echo htmlspecialchars(urldecode($_GET['error_message']));

  }

  if (isset($_GET['info_message']) && zen_not_null($_GET['info_message'])) {

   echo htmlspecialchars($_GET['info_message']);

} else {



}

?>





<!--bof-header logo and navigation display-->

<?php

if (!isset($flag_disable_header) || !$flag_disable_header) {

?>



<div id="headerWrapper">

<!--bof-navigation display-->
<!-- <?php /* --elibird ==rearrange header */ ?>
<div id="navMainWrapper">

<div id="navMain">

    <ul class="back">

    <li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li>

<?php if ($_SESSION['customer_id']) { ?>

    <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>

    <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>

<?php

      } else {

        if (STORE_STATUS == '0') {

?>

    <li><a href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>

<?php } } ?>



<?php if ($_SESSION['cart']->count_contents() != 0) { ?>

    <li><a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a></li>

    <li><a href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>

<?php }?>

</ul>

</div>

<div id="navMainSearch"><?php require(DIR_WS_MODULES . 'sideboxes/search_header.php'); ?></div>

<br class="clearBoth" />

</div>
-->


<!--eof-navigation display-->



<!--bof-branding display-->


<!--
    <div id="logo"><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>'; ?></div>

<?php if (HEADER_SALES_TEXT != '' || (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2))) { ?>

    <div id="taglineWrapper">

<?php

              if (HEADER_SALES_TEXT != '') {

?>

      <div id="tagline"><?php echo HEADER_SALES_TEXT;?></div>

<?php

              }

?>

<?php

              if (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2)) {

                if ($banner->RecordCount() > 0) {

?>

      <div id="bannerTwo" class="banners"><?php echo zen_display_banner('static', $banner);?></div>

<?php

                }

              }

?>

    </div>

<?php } // no HEADER_SALES_TEXT or SHOW_BANNERS_GROUP_SET2 ?>

-->
<div style="width:1000px; background-color:#fff;" >
<div style="float:left; width:400px; height:120px;">
<a href="https://www.anythinginabasket.com" style="border:0;">
<img src="includes/templates/bookshelf/images/new_tisket_brown.png" alt="A Tisket A Tasket Anything In A Basket" style="border:0;">
</a>
<div align="center" style="font-size:14px; font-weight:bold;">
~ Building Beautiful Gift Baskets Since 1988 ~
</div>
</div><br/>
<div style="float:right" id="" width="500px;">
	<ul class="ul_left" style="text-align:right; float:right; margin:4px;" >

    <li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li>

<?php if ($_SESSION['customer_id']) { ?>

    <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>

    <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>

<?php

      } else {

        if (STORE_STATUS == '0') {

?>

    <li><a href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>

<?php } } ?>



<?php if ($_SESSION['cart']->count_contents() != 0) { ?>

    <li><a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a></li>

    <li><a href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>

<?php }?>

</ul>

<div style="float:right;clear:right; margin-top:2px; margin-bottom:2px;">
   <!-- 
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

<div style="float:left" class="fb-like" data-href="https://www.facebook.com/ATisketATasketAnythingInABasket" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>
<div style="margin-bottom:5px; float:left;"><span class='st_facebook_hcount' displayText='Facebook'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_googleplus_hcount' displayText='Google +'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_twitter_hcount' displayText='Tweet'></span></div>
<div style="margin-bottom:5px; float:left;"><span class='st_pinterest_hcount' displayText='Pinterest'></span></div>

</div>
-->


<!--
<div style="float:right;clear:right; margin-top:2px; margin-bottom:2px;">
<?php require(DIR_WS_MODULES . 'sideboxes/search_header.php'); ?>
</div>
-->
<!--
<div style="float:left; font-size:22px; font-weight:bold;">1.800.734.GIFT (4438)
</div> -->
<!--
<div width="" style="float:right; margin-right:10px">
<img width="580px" style="top:50px;position:relative;" src="includes/templates/bookshelf/images/brown_bar2.png">
</div>
-->
<div align="left" style="margin-bottom:26px; text-align:center; font-size:26px; font-family: helvetica; font-weight:bold; width:550px;"><span style="padding-right:6px;"><span style="padding-left:6px;">How can we help you? <br>Call us at 631.385.0001</span></span>
<div style="float:right; position:relative; right:33px;">
<div style="float:right;width:80px;position:absolute; font-size:11px; font-weight:normal;">
<img style="height:40px; margin-bottom:2px;" src="images/american_flag.jpg"><br>
Made in USA
</div>
</div>
</div>   
   
        <?php require($_SERVER['DOCUMENT_ROOT'].DIR_WS_CATALOG.'includes/languages/english/html_includes/bookshelf/define_page_3.php'); ?> 
      
    
    </div>
</div>

<br style="clear:both;">
<!--  DON'T UNCOMMENT UNTIL CHRISTMAS!!! -->
<!-- <img src="images/holly.png" style="margin-left:10px;margin-top:8px;"> -->
<!--eof-branding display-->
<!--bof-header ezpage links-->
<?php



 //if (EZPAGES_STATUS_HEADER == '1' or (EZPAGES_STATUS_HEADER == '2' and (strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR'])))) { 
 	if(0){ //elibird
 	
 	?>

<?php require($template->get_template_dir('tpl_ezpages_bar_header.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_ezpages_bar_header.php'); ?>

<?php } 


?>

<!--eof-header ezpage links-->

<!--eof-header logo and navigation display-->



<!--bof-optional categories tabs navigation display-->
<?php //require($template->get_template_dir('tpl_drop_menu.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_drop_menu.php');?>
<?php // require($template->get_template_dir('tpl_modules_categories_tabs.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_categories_tabs.php'); ?>

<!--eof-optional categories tabs navigation display-->





</div>

<?php


 } ?>

<script>
//$(document).ready(function(){

var anim_stop=0;

begin_drag = function(){
   //document.body.onmouseup = function(){
    //end drag
   //}
   document.body.onmousemove = function(e){
       e = e || window.event; //ie8
    //convert mouse position 
     //console.log(e.pageY); 
     $('#slider').css('top',(e.clientY - $('#bar').position().top)+'px');
   }
}

end_drag = function(){
    
}



var interval = 0;

    move = function(){ 
        $('#move_me').animate({
                bottom:'+=550',
                //opacity: 0
            }, 3000, function(){
                //animate complete 'linear', 
        });
        
        $('#move_me2').animate({
                bottom:'+=550',
                //opacity: 0
            }, 3000, function(){
                //animate complete
        });
        $('#move_me3').animate({
                bottom:'+=550',
                //opacity: 0
            }, 3000, function(){
                //animate complete
        });
        
        $('#active_basket').animate({
                top:'+=186',
                //opacity: 0
            }, 3000, function(){
                //animate complete 'linear', 
        });
    };
    
    move_middle = function(){ 
        $('#move_me').animate({
                bottom:'550',
                //opacity: 0
            }, 3000, function(){
                //animate complete 'linear', 
        });
        $('#move_me2').animate({
                bottom:'550',
                //opacity: 0
            }, 3000, function(){
                //animate complete
        });
        $('#move_me3').animate({
                bottom:'550',
                //opacity: 0
            }, 3000, function(){
                //animate complete
        });
    };
    
    move_back = function(){
       
       $('#bar').animate({height:0}, 1000); 
         
        $('#move_me').animate({
                bottom:'-=1100',
                //opacity: 0
            }, 1000, function(){
                //animate complete 'linear', 
        });
        $('#move_me2').animate({
                bottom:'-=1100',
                //opacity: 0
            }, 1000, function(){
                //animate complete
        });
        $('#move_me3').animate({
                bottom:'-=1100',
                //opacity: 0
            }, 1000, function(){
                //animate complete
        });
        
        //startAnim();
        
    };
    
    //setTimeout(function(){move();},5000);
    height=0;
    var first_move=0;
    second_move=0;
    third_move=0;
    
    stopAnim = function(){
     var stop=0;
     clearInterval(interval);   
    }
    var stop=0;
    
    function startAnim(){
    interval = setTimeout(function(){
       animate();
    }, 10);
    }
    
    animate = function(){
        
        if(height >= 180){                
               if(!first_move){
                  move();
                  first_move=1;
                 }    
            }
            
        if(height >= 360){                
           if(!second_move){
              move();
              second_move=1;
             }
        }
        
        height+=11;
        
        $('#bar').animate({height: height}, function(){
                if(height < 549 && !anim_stop){
                    interval = setTimeout(function(){
                       animate();
                    }, 10);
                    } else {
                    /*    
                    first_move=0;
                     second_move=0;
                    height=0;
                    move_back();    
                    */
                    }
                } 
            );
            
        
        //console.log(height);
        /*
        if(height < 549){

            if(height >= 300){                
               if(!first_move){
                  move();
                  first_move=1;
                 }
                
            }
            
            interval = setTimeout(function(){
               animate();
            }, 10);
        }
        */
        
    }
    
    /*
    move_middle = function(){
           
    }
    */
    
    move_basket = function(basket){
        //set height of bar to max
         
         anim_stop=1;
         
         switch(basket){
             
           case 1:
           
           $('#active_basket').animate({
                top:'0',
                //opacity: 0
            }, 1000, function(){
                //animate complete 'linear', 
                });
           $('#bar').animate({
                height:'180',
                //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                    });
                $('#move_me').animate({
                bottom:'0',
                //opacity: 0
            }, 1000, function(){
                //animate complete 'linear', 
            });
            $('#move_me2').animate({
                    bottom:'0',
                    //opacity: 0
                }, 1000, function(){
                    //animate complete
            });
            $('#move_me3').animate({
                    bottom:'0',
                    //opacity: 0
                }, 1000, function(){
                    //animate complete
            });
            
            break;
            case 2:
            
                $('#active_basket').animate({
                top:'186',
                //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                    });
                $('#bar').animate({
                height:'364',
                //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                    });
               
                    $('#move_me').animate({
                    bottom:'550',
                    //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                });
                $('#move_me2').animate({
                        bottom:'550',
                        //opacity: 0
                    }, 1000, function(){
                        //animate complete
                });
                $('#move_me3').animate({
                        bottom:'550',
                        //opacity: 0
                    }, 1000, function(){
                        //animate complete
                });
            
            break;
            case 3:
            $('#active_basket').animate({
                top:'372',
                //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                    });
                $('#bar').animate({
                height:'549',
                //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                    });
               
                    $('#move_me').animate({
                    bottom:'1100',
                    //opacity: 0
                }, 1000, function(){
                    //animate complete 'linear', 
                });
                $('#move_me2').animate({
                        bottom:'1100',
                        //opacity: 0
                    }, 1000, function(){
                        //animate complete
                });
                $('#move_me3').animate({
                        bottom:'1100',
                        //opacity: 0
                    }, 1000, function(){
                        //animate complete
                });
            break;     
        }   
    }
    
    
    startAnim();
    
//});
</script>
