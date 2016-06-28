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
/*
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
*/
}

/*
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
*/
if(isset($_POST['save'])){

    //first grab all 
    $q = "select * from products where master_categories_id = '54'";
        
        $rs = mysql_query($q) or die(__LINE__." ".mysql_error());
        
        while($row = mysql_fetch_array($rs)){
            $baskets[$row['products_id']] = $row['sold'];
        }
    

    foreach($_POST as $k => $v){
        //echo "<br> substr = " . substr($k,0,4);
        if(substr($k,0,4) == 'last'){
            $id = explode('_',$k);
            $id = $id[1];
            //echo "<br>id = $id";
            /*if($v != $_POST['last_'.$id]){ //detect change
                echo "update ";
            }*/
            if($v && !isset($_POST['sold_'.$id])){
                //echo "<br> set to unsold";
                $q = "update products set sold = 0 where products_id = '$id'";
                //echo "<br>q = $q";
                mysql_query($q) or die(__LINE__." ".mysql_error());
            }
            if(!$v and isset($_POST['sold_'.$id])){
                //echo "<br> set to sold";
                $q = "update products set sold = 1 where products_id = '$id'";
                //echo "<br>q = $q";
                mysql_query($q) or die(__LINE__." ".mysql_error());
            }
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
<form action="<?=$link?>cart_admin/showroom.php" method="POST" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="bab">
	<tr>
		<td colspan="10">
		<div style="float:left"><input type="submit" value="save" name="save"></div>
			<div style="border-bottom:1px solid black; ;"><h1>Showroom Admin</h1>
			
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="10">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="10"><h3>BASKETS!</h3></td>
	</tr>

	<?php
		
		/*
		$baskets = array(
					array('id'=>'2', 'name'=>'small', 'desc'=>'small', 'points'=>'20', 'price'=>'15.50', 'img'=>'34.95.jpg'),
				);
		*/
	
		$q = "select * from products where master_categories_id = '54'";
		
		$rs = mysql_query($q) or die(__LINE__." ".mysql_error());
		
		while($row = mysql_fetch_array($rs)){
			$baskets[] = $row;
				
			//process here
            $checkbox = '';    
            if($row['sold']){
                $checkbox = "<label>sold: <input type=\"checkbox\" id=\"sold_{$row['products_id']}\" name=\"sold_{$row['products_id']}\" checked=\"checked\"></label>";
            } else {
                $checkbox = "<label>sold: <input type=\"checkbox\" name=\"sold_{$row['products_id']}\"></label>";
            }
            echo "<input type=\"hidden\" name=\"last_{$row['products_id']}\" value=\"{$row['sold']}\">";
            echo "<div id=\"\" class=\"displayitem\" style=\"float:left; margin:10px;\"> " .
        "<img src=\"../images/{$row['products_image']}\" width=\"200px\" ><br>$checkbox".
        "</div>";
            			
		}
	
		//$b_st = '<tr>';
		

	    
	 
		/*
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
		*/
	?>
	<tr>
		<td colspan="10">
			<div style="border-bottom:1px solid black">&nbsp;</div>
		</td>
	</tr>
</table>
</form>
