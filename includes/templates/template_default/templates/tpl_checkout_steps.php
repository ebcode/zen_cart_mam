<style>
.checkout_step{
	font-size:.8em;
	margin-bottom:10px;
}
.checkout_step ul{
	margin:0;
	border:0;
	display:inline-block;
	padding:0;
}
.checkout_step ul li{
	margin:0;
	list-style-type:none;
	display:inline-block;
	border:1px solid black;
	font-weight:bold;
	text-align:center;
	border:1px solid gray;
	padding:1px;
}

.active_menu_item {
	color:#fff;
	border:2px solid black;
	background-color:#550000;

}

.checkout_step ul li div{
	font-weight:normal;
	color:#000;
	background-color:#eee;
	padding:3px;
}

.checkout_step ul li.active_menu_item div{
	background-color:#fff;
}
</style>

<div class="checkout_step">
	<ul>
	    
	    
	    <?php 
        
        if(isset($_SESSION['multiple_addresses'])){
            ?>
            
        <li class="<?php echo (($_GET['main_page']=='checkout_multiple_addresses')?'active_menu_item':''); ?>">
            <?php echo HEADING_TITLE_STEP_1; ?>
            <div><?php echo  HEADING_SUB_TITLE_STEP_1_1; ?></div>
        </li>
        <li class="<?php echo (($_GET['main_page']=='checkout_multiple_shipments')?'active_menu_item':''); ?>">
            <?php echo HEADING_TITLE_STEP_1_2; ?>
            <div><?php echo  HEADING_SUB_TITLE_STEP_1_2; ?></div>
        </li>
        
        <li class="<?php echo (($_GET['main_page']=='checkout_payment')?'active_menu_item':''); ?>">
            <?php echo HEADING_TITLE_STEP_2; ?>
            <div><?php echo  HEADING_SUB_TITLE_STEP_2; ?></div>
        </li>
        <li class="<?php echo (($_GET['main_page']=='checkout_confirmation')?'active_menu_item':''); ?>">
            <?php echo HEADING_TITLE_STEP_3; ?>
            <div><?php echo  HEADING_SUB_TITLE_STEP_3; ?></div>
        </li>
        
        
        <?php
        }  else {
        
        ?>      
	    
	    
		<li class="<?php echo (($_GET['main_page']=='checkout_shipping')?'active_menu_item':''); ?>">
			<?php echo HEADING_TITLE_STEP_1; ?>
			<div><?php echo  HEADING_SUB_TITLE_STEP_1; ?></div>
		</li>

		<li class="<?php echo (($_GET['main_page']=='checkout_payment')?'active_menu_item':''); ?>">
			<?php echo HEADING_TITLE_STEP_2; ?>
			<div><?php echo  HEADING_SUB_TITLE_STEP_2; ?></div>
		</li>
		<li class="<?php echo (($_GET['main_page']=='checkout_confirmation')?'active_menu_item':''); ?>">
			<?php echo HEADING_TITLE_STEP_3; ?>
			<div><?php echo  HEADING_SUB_TITLE_STEP_3; ?></div>
		</li>
		
		<?php 

            }

        ?>
		
	</ul>
</div>