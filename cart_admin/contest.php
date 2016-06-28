<?php
/*
 * 
 */


error_reporting(E_ALL);
ini_set('display_errors','on');

require('includes/application_top.php');

//require_once('./includes/classes/upload.php');

if(count($_POST)){
    if(isset($_POST['new_contest'])){
        $contest_name = mysql_real_escape_string($_POST['contest_name']);
        $start_date = strtotime($_POST['start_date']);
        $end_date = strtotime($_POST['end_date']);
        
        $q = "insert into contests (contest_name, start, stop) values ('$contest_name','$start_date', '$end_date')";
        //echo "<br> q= = $q";
        mysql_query($q) or die(mysql_error());
    }
}

if(isset($_GET['action'])){
    switch ($_GET['action']){
        case 'delete':
            $contest_id = (int) $_GET['id'];
            $q = "delete from contests where contest_id = '$contest_id'";
            mysql_query($q) or die(mysql_error());
        
        break;

    }
}

$q = "select max(contest_id) as contest_id from contests";
    $rs = mysql_query($q);  
    $row = mysql_fetch_array($rs);
    
    $latest_contest_id = $row['contest_id'];

if(isset($_GET['contest_id'])){
    $latest_contest_id = (int)$_GET['contest_id'];
}

$q = "select * from contests";
    $rs = mysql_query($q);  
    while($rows[] = mysql_fetch_array($rs)){};

?><!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<style>
.contest_name{
 font-size:16px;   
}
.tr_head{
 font-weight:bold;   
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<div class="content">
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="bab">
    <tr>
        <td colspan="10">
        
            <div style="border-bottom:1px solid black; ;"><h1>Contest Admin</h1>
            <table width="800px">
                <tr><td>Contests</td></tr>
                <?php
                foreach($rows as $row){
                    if($row){
                    ?>
                    <tr>
                    <td class="contest_name"><a href="./contest.php?contest_id=<?php echo $row['contest_id']; ?>"><?php echo $row['contest_name']; ?></a></td>
                    <td><a href="./contest.php?action=delete&id=<?php echo $row['contest_id']; ?>">delete</a></td>
                    </tr>
                    <?php
                        if($row['contest_id'] == $latest_contest_id){
                            ?>
                            <tr><td><h3>ENTRIES</h3>
                            <table width="800px" border="1"><tr class="tr_head"><td>Name</td><td>Email</td><td>Guess</td></tr>
                            <?php
                                
$q = "select entry, customers_firstname, customers_lastname, customers_email_address from contest_entries ce left join customers c on ce.customers_id = c.customers_id";
    
    $rs = mysql_query($q);  
    
    while($row = mysql_fetch_array($rs)){
    ?>
        <tr>
        <td><?php echo $row['customers_firstname'] . ' ' . $row['customers_lastname'];?></td>
        <td><?php echo $row['customers_email_address'];?></td>
        <td><?php echo $row['entry'];?></td>
        </tr>
    <?php
    
}

                            ?>
                            </td></tr>
                             <?php
                        }
                    }
                }

                ?>
            </table>
            </div>
        </td>
    </tr>
</table><br><br>
<form action="./contest.php" method="POST">
    <input type="hidden" name="new_contest" value="1">
<table>
    <tr><td>
        <h3>New Contest Form</h3>
    </td></tr>
    <tr>
        <td>Name</td>
        <td>
            <input name="contest_name">
        </td>
    </tr>
    <tr>
        <td>Start Date</td>
        <td>
            <input name="start_date"> ex: (1/1/2014)
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input name="end_date"> ex: (1/1/2014)
        </td>
    </tr>
    <tr><td>
        <input type="submit" value="submit">
    </td></tr>
</table>
</form>