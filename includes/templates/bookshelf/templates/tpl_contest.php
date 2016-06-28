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

<table width="100%" border="0" cellspacing="0" cellpadding="0" id="contentMainWrapper">
  <tr>
<?php
if (COLUMN_LEFT_STATUS == 0 || (CUSTOMERS_APPROVAL == '1' and $_SESSION['customer_id'] == '') || (CUSTOMERS_APPROVAL_AUTHORIZATION == 1 && CUSTOMERS_AUTHORIZATION_COLUMN_LEFT_OFF == 'true' and ($_SESSION['customers_authorization'] != 0 or $_SESSION['customer_id'] == ''))) {
  // global disable of column_left
  $flag_disable_left = true;
}
if (!isset($flag_disable_left) || !$flag_disable_left) {
?>

<td id="navColumnOne" class="columnLeft" ><div style="border:0px solid black; width:235px;" class="new_cats">
<!--  <td id="navColumnOne" class="columnLeft" style="width: <?php echo COLUMN_WIDTH_LEFT; ?>"> -->
<?php
 /**
  * prepares and displays left column sideboxes
  *
  */
?>
<!-- <div id="navColumnOneWrapper" style="width: <?php echo BOX_WIDTH_LEFT; ?>">-->
    <div id="navColumnOneWrapper" style="">
    <?php require(DIR_WS_MODULES . zen_get_module_directory('column_left.php')); ?> 
</div></div></td>
<?php
}
?>
    <td valign="top" style="width:764px;"><div style="border:0px dashed green;"> 
<!-- bof  breadcrumb -->
<?php if (DEFINE_BREADCRUMB_STATUS == '1' || (DEFINE_BREADCRUMB_STATUS == '2' && !$this_is_home_page) ) { ?>
    <div id="navBreadCrumb"><?php echo $breadcrumb->trail(BREAD_CRUMBS_SEPARATOR); ?></div>
<?php } ?>
<!-- eof breadcrumb -->

<?php
  if (SHOW_BANNERS_GROUP_SET3 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET3)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerThree" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<div align="center"><br>
<?php



require(DIR_FS_CATALOG.'includes/languages/english/html_includes/define_contest_page.php');

if(isset($contest_msg)){
    ?>
    <div class="msg">
        <?php echo $contest_msg; ?>
    </div>
    <?php
}

//if user is logged in, display form, otherwise display message
if(isset($_SESSION['customer_id'])){
    //echo "<br>logged in";
    ?>
    <div style="font-size:14px; font-weight:bold; margin:6px;">
    <form action="contest.php" method="POST">
        Your Guess: <input name="guess"><br>
        <input type="submit" value="Enter">
        
    </form>
    </div>
    <?php
} else {
//echo "<br>NOT logged in";
    
}

if($contest_entries_count >= 25){
?>
<table cellpadding="4" cellspacing="0" style="font-size:16px; font-weight:bold;"><tr><td colspan="2"><h3>Contest Entries</h3></td></tr>
        <tr><td><b>Name</b></td><td><b>Guess</b></td></tr>
    <?php
    //show entered guesses
    $zzz = 1;
    $tr_class = "";
    foreach($rows as $row){
        $zzz+=1;
         $tr_class = "row_". ($zzz%2);
        if($row){
         ?>
         <tr class="<?php echo $tr_class; ?>"><td><?php echo ucwords($row['customers_firstname']). ' ' . ucwords(substr($row['customers_lastname'], 0, 1)); ?>.</td>
             <td><?php echo $row['entry']; ?></td>
         </tr>
         <?php           
        }
    }

    ?>
    </table>
    <?php

}





?>
</div>
