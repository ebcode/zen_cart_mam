<?php

ini_set('error_log', 'email_errors');
error_reporting(E_ALL);
ini_set('display_errors','on');

require('includes/application_top.php');

if(isset($_GET['action'])){
    switch ($_GET['action']){
        
        case 'toggle':
            $id = (int)$_GET['id'];
            $val = (int)$_GET['v'];
            
            if((int)$val){
                $val = 0;
            } else {
                $val = 1;
            }

            $q = "update newsletter_emails set active = '$val' where email_id = '$id'";
            mysql_query($q) or die(mysql_error());
    
        break;
        default:
            //show all newsletter emails
            

        break;
    }
}

if(isset($_POST['email'])){

        
            $email = mysql_real_escape_string($_POST['email']);
            $first = mysql_real_escape_string($_POST['first']);
            $last = mysql_real_escape_string($_POST['last']);
            $q = "insert into newsletter_emails (email, fname, lname) values ('$email', '$first', '$last')";
            mysql_query($q) or die(mysql_error());
        
}


$q = "select * from newsletter_emails order by email_id";
            
            $rs = mysql_query($q) or die(mysql_error());
            ?>
            
            <h2>Add Subscriber
            <form method="POST" action="email_admin.php">
            <table>
                  <tr>
                      <td>email:</td><td><input name="email"></td>  
                  </tr>
                  <tr>
                      <td>first:</td><td><input name="first"></td>  
                  </tr>
                  <tr>
                      <td>last:</td><td><input name="last"></td>  
                  </tr>
                  <tr>
                      <td><input type="submit" value="add"></td>
                  </tr>
            </table>
            </form>
            
            <table>
                <tr>
                    <th>email
                    </th>
                    <th>
                        first
                    </th>
                    <th>
                        last
                    </th>
                    <th>active</th>
                </tr>
                    <?php
            while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
                ?>
                <tr>
                    <td><? echo $row['email']; ?></td>
                    <td><? echo $row['fname']; ?></td>
                    <td><? echo $row['lname']; ?></td>
                    <td><a href="email_admin.php?action=toggle&id=<? echo $row['email_id']; ?>&v=<? echo $row['active']; ?>"><? echo $row['active']; ?></a></td>
                    
                </tr>
                
                <?php
            }

            ?>
            </table>
<?php
/*
if(isset($_GET['action'])){
    switch ($_GET['action']){
        
        case 'delete':
        break;
        default:
            //show all newsletter emails
            

        break;
    }
}
*/