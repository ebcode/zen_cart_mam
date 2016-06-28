<?php
/*
 * 
 */

require("./SimpleImage.php");
$image = new SimpleImage();

error_reporting(E_ALL);
ini_set('display_errors','on');

require('includes/application_top.php');

require_once('./includes/classes/upload.php');

//mysql_query("insert into bab_baskets (im) values ('tst')")  or die (__LINE__.": ".mysql_error());

function rndstr($len){
	$rnd = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str='';
	for($i=0;$i<$len;$i++){
		$str .= substr($rnd, rand(0,62), 1); 
	}
	return $str;
}

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
		case 'new_cat':
			
			$q = "SELECT IFNULL(max(ord),0)+1 as ord from bab_categories";
			
			$rs = mysql_query($q) or die(__LINE__.": ".mysql_error());
			
			$row = mysql_fetch_array($rs);
			$ord = $row['ord'];
			
			$q = "insert into bab_categories (cat_name, ord) values ('', '$ord')";
			
			//echo "<br>q=$q";
			
			mysql_query($q) or die(__LINE__.": ".mysql_error());
			
			break;
		case 'rem_cat':
				
				$id = (int)$_GET['id'];
			
				$q = "delete from bab_categories where id = '$id'";
					
				mysql_query($q) or die(__LINE__.": ".mysql_error());
					
				break;
	   
       case 'activate':
                $id = (int)$_GET['id'];
                
                if((int)$_GET['activate']==1){
                    $q = "update bab_categories set active = '1' where id = '$id'";
                } else {
                    $q = "update bab_categories set active = '0' where id = '$id'";
                }
                
                mysql_query($q) or die(__LINE__.": ".mysql_error());
                
                break;
       
		default:
			break;
	}
}


if(count($_POST) > 0){
	
	foreach($_POST as $k => $v){
		
		$im="";
		$nm="";
		$ds="";
		$pt="";
		$pr="";
		$ac=0;
		
		$id = explode('_',$k);
		$id = $id[2];
		
		if($id == $old_id || $id == 'id') continue;
		
		$old_id = $id;
		
		if(strpos($k,'basket')===0){
			//basket
			
			//process basket
			
			if(''!=$_FILES['basket_im_'.$id]['name']){
				//process image
				
				$bab_image = new upload('basket_im_'.$id);
				$bab_image->set_destination(DIR_FS_CATALOG . 'bab/images/');
				
				if ($bab_image->parse()) {			
					
					$bab_type = substr($bab_image->filename, strlen($bab_image->filename)-4);
					$bab_image_str = substr($bab_image->filename, 0, strlen($bab_image->filename)-4); //remove .jpg
					
					$rnd_string = rndstr(7);
					
					$new_image_name = $bab_image_str . $rnd_str . $bab_type;
					
					echo "<br> new image name = $new_image_name";
					
					$bab_image->set_filename($new_image_name); //add randomness to filename
					
					if($bab_image->save(1)){
						$im = $bab_image->filename;
					}
					
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
			
			echo "<br>q=$q";
			
			mysql_query($q) or die (__LINE__.": ".mysql_error());
			
			
		} else if(strpos($k,'item')===0){
			//item	
			if(''!=$_FILES['item_im_'.$id]['name']){
				//process image

				$bab_image = new upload('item_im_'.$id);
				$bab_image->set_destination(DIR_FS_CATALOG . 'bab/images/');
			
				if ($bab_image->parse()) {
					
					$rnd_string = rndstr(7);
					
					$bab_type = substr($bab_image->filename, strlen($bab_image->filename)-4);
					$bab_image_str = substr($bab_image->filename, 0, strlen($bab_image->filename)-4); //remove .jpg
					
					//$new_name = str_ireplace('.jpg', '.'.$rnd_string.'.jpg', $bab_image->filename);
					
					$new_image_name = $bab_image_str . $rnd_string . $bab_type;
									
					echo "<br new name = $new_image_name";
					
					$bab_image->set_filename($new_image_name);
					
					if($bab_image->save(1)){
						
						$im = $bab_image->filename;
						
						//make a copy 78px height
						$image->load('../bab/images/'.$im);
											
						$image->resizeToHeight(78);
						
						//$rnd_string = rndstr(7);
						
						//echo "<br> rnd str = $rnd_string";
						
						$new_name = $bab_image_str . $rnd_string . '.sm'.$bab_type;
						
						//$new_name = str_ireplace('.jpg', '.'.$rnd_string.'.sm.jpg', $im);
						
						echo "<br> new name = $new_name";
						
						$image->save(DIR_FS_CATALOG.'bab/images/'.$new_name);
						
						$im = $new_name;
					
					}
					
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
			$cat_id = mysql_real_escape_string($_POST['item_cat_id_'.$id]);
			$ac = isset($_POST['item_ac_'.$id])?1:0;			
			
			$q = "update bab_items set im='$im', nm='$nm', ds='$ds', pt='$pt', ac='$ac', cat_id='$cat_id' where id = '$id'";			
			
			mysql_query($q) or die (__LINE__.": ".mysql_error());
			
			
		}  else if(strpos($k,'cat')===0){
			//category
			$nm = mysql_real_escape_string($_POST['cat_name_'.$id]);
			
			$q = "update bab_categories set cat_name = '$nm' where id = '$id'";
			
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
		height: 125px;
	    padding: 2px;
	    width: 450px;
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
	h1 a{
	font-size:16px;
	font-weight:bold;
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
		<div style="float:left"><input type="submit" value="save baskets"></div>
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
</form>
<form action="<?=$link?>cart_admin/bab.php" method="POST" enctype="multipart/form-data"><br>
<input type="submit" value="save categories"> 
<h3>Item Categories</h3>	
<input type="button" value="new category" onclick="document.location='./bab.php?action=new_cat'"> 
<?php 
	$q = "select * from bab_categories order by ord";
	
	$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
	
	while($row = mysql_fetch_array($rs)){
		$item_categories[] = $row;
			
		//process here
			
	}
	
	
	foreach($item_categories as $item_cat){
		if($item_cat['active']){
            $chkbox = 'active: <input type="checkbox" checked="checked" onclick="activate(this, '.$item_cat['id'].')" />';
        } else {
            $chkbox = 'active: <input type="checkbox" onclick="activate(this, '.$item_cat['id'].')" />';
        }
        $ic_str = <<<EOL
        <input name="cat_name_{$item_cat['id']}" value="{$item_cat['cat_name']}" />
        {$chkbox}
        <a href="./bab.php?action=rem_cat&id={$item_cat['id']}">remove</a>
EOL;
        echo $ic_str;
	}
	
?><br>
</form>

<?php 
		
		$img = '';
		$item = '';
		
		if(isset($_POST['img_search']) || isset($_POST['item_search'])){
			if(isset($_POST['img_search']) && !empty($_POST['img_search'])){
				$img = mysql_real_escape_string($_POST['img_search']);
				$q = "select * from bab_items where im like '%$img%' order by nm";
			}
			
			if(isset($_POST['item_search'])  && !empty($_POST['item_search'])){
				$item = mysql_real_escape_string($_POST['item_search']);
				$q = "select * from bab_items where nm like '%$item%' order by nm";
			}
			

		} else {	
			$q = "select * from bab_items order by nm limit $pg, 24";
		}
		
		?>

<br>
<div style="border:1px solid black;">
<form action="<?=$link?>cart_admin/bab.php" method="POST">
	<h3>Search for Items</h3><br>
image name:	<input name="img_search" value="<?php echo $img; ?>"><br>
item name:	<input name="item_search" value="<?php echo $item; ?>"><br>
<input type="submit" value="search">
</form>
</div>

<form action="<?=$link?>cart_admin/bab.php" method="POST" enctype="multipart/form-data"><br>
<input type="submit" value="save items">
<h3>Items</h3>			
			<?php
			
			
		/*
		$basket_items = array(
			array('id'=>'1','im'=>'cheesesticks8.jpg','nm'=>'cheesesticks','ds'=>'desc','pt'=>'3','ac'=>'1',)
		);
		*/
		
		//pages
		$q = "select count(*) from bab_items";
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
		$row = mysql_fetch_array($rs);
		
		$total = $row[0];
		
		$pages = ceil($total/24);
		
		$page_links='';
		
		//rather than page numbers, use alphabetical Ab-Af, Af-Ax, Ax-Bo, Bo-Cu, etc..
		
		//cast(nm as Char(3))
		$q = "select cast(bab_items.nm as Char(3)) as nm from (select @counter:=0) v, bab_items having ((@counter:=@counter+1)%24=0 || (@counter)%24-1=0) order by nm";
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
		
		while($row = mysql_fetch_array($rs)){
			$item_names[] = $row['nm']; 
		};
		
		$row_len = count($item_names);
		
		for($i=0; $i<$row_len; $i+=2){
			$az[] = $item_names[$i].' &mdash; '.$item_names[$i+1]; 	
		}
		
		
		for($i=0;$i<$pages;$i++){
			
			if((int)$_REQUEST['pg']==($i)){	
				//$page_links .= '<a href="./bab.php?pg='.$i.'#pg"><u>'.($i+1).'</u></a>&nbsp;';
				$page_links .= '<div style="float:left; padding:2px; border: 1px solid #ccc; margin:2px;"><a href="./bab.php?pg='.$i.'#pg"><u>'.($az[$i]).'</u></a></div>';
			} else {
				//$page_links .= '<a href="./bab.php?pg='.$i.'#pg">'.($i+1).'</a>&nbsp;';
				$page_links .= '<div style="float:left; padding:2px; border: 1px solid #ccc; margin:2px;"><a href="./bab.php?pg='.$i.'#pg">'.($az[$i]).'</a></div>';
			}
		}
		
		$pg = isset($_REQUEST['pg'])?(int)$_REQUEST['pg']:0;
		
		$pg_link = $pg;
		
		$pg = (($pg+1)*24)-24;
		
		?>
		<div style="float:left;clear:both;"><a name="pg"><h1>Page: <div style="float:right;"><?php echo $page_links; ?></div></h1></a></div>
		<br style="clear:both;">
		<?php
		
		//echo '<br> q = '.$q.'<br>';
		
		$q = "select * from bab_items order by nm limit $pg, 24";
		
		
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error() . '<br> q = '.$q.'<br>');
		
		while($row = mysql_fetch_array($rs)){
			$basket_items[] = $row;
			
			//process here
			
		}
		
		
		
		foreach($basket_items as $item){
			
			$checked=($item['ac'])?' checked="checked" ':'';
			
			$remove='';
			
			if($item['id']!='1'){
				$remove  = "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{$link}cart_admin/bab.php?action=remove&id={$item['id']}&pg={$pg_link}#pg\">remove</a>";
			}
			
			$cat_dd = build_cat_dd($item['cat_id'], $item['id']);
			
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
					<input name="item_pt_{$item['id']}" size="1" value="{$item['pt']}">pts
				</div><br>
				<div style="">
					<textarea width="260px" name="item_ds_{$item['id']}" >{$item['ds']}</textarea><br>
					$cat_dd
					<input type="checkbox" name="item_ac_{$item['id']}" $checked > active<br>
					<a href="{$link}cart_admin/bab.php?action=duplicate&id={$item['id']}&pg=$pg_link#pg">duplicate</a>
					$remove
				</div>		
			</div>
EOT;
		}
		
		echo $bi_st;
		
		?>
</div>

<div style="clear:both; margin-top:8px; border-top:1px solid black;">
<input type="submit" value="save items">
<input type="hidden" name="pg" value="<?php echo $pg_link; ?>">
</div>
</form>
<?php 

function build_cat_dd($cat_id, $id){
	global $item_categories;
	$dd = '<select name="item_cat_id_'.$id.'">';
	
	foreach($item_categories as $cat){
		$dd .= '<option value="'.$cat['id'].'" '.(($cat_id==$cat['id'])?'selected="selected"':'').' >'.$cat['cat_name'].'</option>';
	}
	
	$dd .='</select>';
	
	return $dd;
}

?>
<script>
    activate = function(el, id){
      
        loc = document.location.href.split('?')[0];
        
        if(el.checked){
            document.location = loc+'?action=activate&id='+id+'&activate=1';
        } else {
            document.location = loc+'?action=activate&id='+id+'';
        }
        
    }
</script>