<?php
/*
 * 
 */

die("?");

error_reporting(E_ALL);
ini_set('display_errors','on');

require('includes/application_top.php');

require_once('./includes/classes/upload.php');

//mysql_query("insert into bab_baskets (im) values ('tst')")  or die (__LINE__.": ".mysql_error());

$link = HTTP_SERVER . DIR_WS_CATALOG;

$old_id=0;

if(isset($_GET['action'])){
	switch ($_GET['action']){
		
		case 'duplicate':
			
			$id = (int)$_GET['id'];
			
			$q = "insert into bab_items (im,nm,ds,pt,ac) (select im,nm,ds,pt,0 from bab_items where id='$id')";
			
			mysql_query($q) or die(__LINE__.": ".mysql_error());
			
			break;
		case 'remove':
			
			$id = (int)$_GET['id'];
				
			$q = "delete from bab_items where id='$id'";
				
			mysql_query($q) or die(__LINE__.": ".mysql_error());
			
			break;
		default:
			break;
	}
}

if(count($_POST) > 1){
	
	foreach($_POST as $k => $v){
		
		$im="";
		$nm="";
		$ds="";
		$pt="";
		$pr="";
		$ac=0;
		
		$id = explode('_',$k);
		$id = $id[2];
		
		if($id === $old_id) continue;
		
		$old_id = $id;
		
		if(strpos($k,'basket')===0){
			//basket
			
			//process basket
			if(''!==$_FILES['basket_im_'.$id]['name']){
				//process image
				
				$bab_image = new upload('basket_im_'.$id);
				$bab_image->set_destination(DIR_FS_CATALOG . 'bab/images/');
				
				if ($bab_image->parse() && $bab_image->save(1)) {			

					$im = $bab_image->filename;
				
				} else {
					echo "<div style=\"background-color:red;color:#fff;font-weight:bold;\">There was an error processing your image: ".$bab_image->filename."</div>";
					$nm = '';
				}
				
				$bab_image=null;
			} else {
				$im = mysql_real_escape_string($_POST['basket_oldim_'.$id]);
			}
			
			$nm = mysql_real_escape_string($_POST['basket_nm_'.$id]);
			$ds = mysql_real_escape_string($_POST['basket_ds_'.$id]);
			$pt = mysql_real_escape_string($_POST['basket_pt_'.$id]);
			$pr = mysql_real_escape_string($_POST['basket_pr_'.$id]);
			$ac = isset($_POST['basket_ac_'.$id])?1:0;
			
			$q = "update bab_baskets set im='$im', nm='$nm', ds='$ds', pt='$pt', pr='$pr', ac='$ac' where id = '$id'";
			
			mysql_query($q) or die (__LINE__.": ".mysql_error());
			
			
		} else if(strpos($k,'item')===0){
			//item
				
			if(''!==$_FILES['item_im_'.$id]['name']){
				//process image

				$bab_image = new upload('item_im_'.$id);
				$bab_image->set_destination(DIR_FS_CATALOG . 'bab/images/');
			
				if ($bab_image->parse() && $bab_image->save(1)) {
					$im = $bab_image->filename;
			
				} else {
					echo "<div style=\"background-color:red;color:#fff;font-weight:bold;\">There was an error processing your image: ".$bab_image->filename."</div>";
					$nm = '';
				}
			
				$bab_image=null;
			} else {
				$im = mysql_real_escape_string($_POST['item_oldim_'.$id]);
			}
				
			$nm = mysql_real_escape_string($_POST['item_nm_'.$id]);
			$ds = mysql_real_escape_string($_POST['item_ds_'.$id]);
			$pt = mysql_real_escape_string($_POST['item_pt_'.$id]);
			$pr = mysql_real_escape_string($_POST['item_pr_'.$id]);
			$ac = isset($_POST['item_ac_'.$id])?1:0;
				
			$q = "update bab_items set im='$im', nm='$nm', ds='$ds', pt='$pt', ac='$ac' where id = '$id'";
				
			mysql_query($q) or die (__LINE__.": ".mysql_error());
			
			
		}
		
	}
		
}


 ?><!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
	<style>
	.bab td{
		vertical-align:top;
	}
	.bskt lbl{
		float:left;
		width:100px;
		margin-right: 10px;
	}
	.bskt inp{
		display:block;
		margin:4px 0px;
		width:100px;
	}
	
	.bab_basket textarea{
		width:162px;
	}
	
	.bab_item{
		padding:2px;
		width:300px;
	}
	.bab_item input{
		margin:3px;
	}
	
	.bab_item textarea{
		width:260px;
	}
	
	
	.content{
		padding:10px;
		margin-bottom:20px;
	}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<div class="content">
<form action="<?=$link?>cart_admin/bab.php" method="POST" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="bab">
	<tr>
		<td colspan="10">
		<div style="float:left"><input type="submit" value="save"></div>
			<div style="border-bottom:1px solid black; ;"><h1>Build A Basket Admin</h1>
			
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="10">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="10"><h3>BASKETS</h3></td>
	</tr>

	<?php
		
		/*
		$baskets = array(
					array('id'=>'2', 'name'=>'small', 'desc'=>'small', 'points'=>'20', 'price'=>'15.50', 'img'=>'34.95.jpg'),
				);
		*/
	
		$q = "select * from bab_baskets order by id asc";
		
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
		
		while($row = mysql_fetch_array($rs)){
			$baskets[] = $row;
				
			//process here			
		}
	
		$b_st = '<tr>';
		

	    
	 
		
		foreach($baskets as $basket){

			$checked=($basket['ac'])?' checked="checked" ':'';

			$b_st .= <<<EOT
			<td class="bskt">
		<table class="bab_basket"><tr><td>	
		<img src="$link/bab/images/{$basket['im']}" align="top" style="float:left;"> 
		</td>
		<td>
		<inp><lbl> Upload Image </lbl><input type="file" name="basket_im_{$basket['id']}" value="" size="5"></inp>
		<inp><lbl> Image File </lbl>		{$basket['im']}</inp>
		<input type="hidden" name="basket_oldim_{$basket['id']}" value="{$basket['im']}" size="5">
		<inp><lbl> Basket Name </lbl> <input name="basket_nm_{$basket['id']}" value="{$basket['nm']}"></inp>
		<inp><lbl> Description </lbl><textarea name="basket_ds_{$basket['id']}">{$basket['ds']}</textarea> </inp>
		<inp><lbl> Pts </lbl><input name="basket_pt_{$basket['id']}" value="{$basket['pt']}"> </inp>
		<inp><lbl> Price </lbl><input name="basket_pr_{$basket['id']}" value="{$basket['pr']}"> </inp>
		<inp><lbl> Active </lbl><input type="checkbox" name="basket_ac_{$basket['id']}" {$checked} > </inp>
		</td>	
		</tr></table>
			</td>
			
EOT;
		}
		
		$b_st .= '</tr>';
		
		echo $b_st;
		
	?>
	<tr>
		<td colspan="10">
			<div style="border-bottom:1px solid black">&nbsp;</div>
		</td>
	</tr>
</table>
<h3>Items</h3>			
			<?php
			
			
		/*
		$basket_items = array(
			array('id'=>'1','im'=>'cheesesticks8.jpg','nm'=>'cheesesticks','ds'=>'desc','pt'=>'3','ac'=>'1',)
		);
		*/
		
		$q = "select * from bab_items";
		
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
		
		while($row = mysql_fetch_array($rs)){
			$basket_items[] = $row;
			
			//process here
			
		}
		
		
		
		foreach($basket_items as $item){
			
			$checked=($item['ac'])?' checked="checked" ':'';
			
			$remove='';
			
			if($item['id']!='1'){
				$remove  = "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{$link}cart_admin/bab.php?action=remove&id={$item['id']}\">remove</a>";
			}
			
			$bi_st .= <<<EOT
			
			
			<div style="border:0px solid black; margin:2px; float:left;" class="bab_item">
				<img src="$link/bab/images/{$item['im']}" style="float:left;" height="100px">
				<div style="float:left;">
					<input type="file" name="item_im_{$item['id']}" size="1">
					<input type="hidden" name="item_oldim_{$item['id']}" size="1" value="{$item['im']}">
				</div>				
				<div style="float:left;">
					<input size="15" value="{$item['nm']}" name="item_nm_{$item['id']}">
				</div>				
				<div style="float:left;">
					<input name="item_pt_{$item['id']}" size="3" value="{$item['pt']}">pts
				</div><br>
				<div style="float:left">
					<textarea width="260px" name="item_ds_{$item['id']}" >{$item['ds']}</textarea><br>
					<input type="checkbox" name="item_ac_{$item['id']}" $checked > active
					&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$link}cart_admin/bab.php?action=duplicate&id={$item['id']}">duplicate</a>
					$remove
				</div>		
			</div>
EOT;
		}
		
		echo $bi_st;
		
		?>
</div>
