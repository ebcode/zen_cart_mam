<body style="background-color:black;">
<?php
//<script src="./includes/templates/bookshelf/jscript/jquery-latest.min.js"></script>

require('includes/classes/zen_lightbox/slimbox.php');
?>
</script>

<link rel="stylesheet" type="text/css" href="./includes/templates/bookshelf/css/stylesheet_zen_lightbox.css" />
    <div style="width:1000px; background-color:white; background-image:url('./images/gradient.png');margin:auto;">
        <img src="./images/gradient.png">
        <div align="center" class="instructions">
        <a href="#"><img style="border:0; float:left; margin-left:15px;" alt="A Tisket A Tasket Anything In A Basket" src="includes/templates/bookshelf/images/new_tisket3.png" ></a><div class="instructions">
        <?php 

//die($_SERVER['DOCUMENT_ROOT'].'/gift_baskets/includes/languages/english/html_includes/bookshelf/define_page_6.php');

require($_SERVER['DOCUMENT_ROOT'].'/includes/languages/english/html_includes/bookshelf/define_page_7.php');
?></div>
        <br style="clear:both;"> 
        </div>
        
        <table class="display_table">
            <?php
 
 echo "$products"; 
 
 ?>
        </table>
        
    </div>
<!--
<div style="align:center; background-image: url('./images/display_table3.png'); width:640px; height:480px; background-size:cover; margin:auto;" >
    

-->

</div>
<div id="abox" style="border:1px solid black; height:100px; width:100px; display:none;"></div>
<script src="./includes/templates/bookshelf/jscript/specials.js"></script>
