<?php

if(isset($_GET['nl'])){

    $parts = explode('_',$_GET['nl']);

    $newsletter_id = (int) $parts[0];
    $link_id = (int) $parts[1];
    
    $q = "update newsletter_links set counts = counts + 1 where newsletters_id = '$newsletter_id' and link_id = '$link_id'";

    $db->Execute($q) or die(__LINE__.':'.mysql_errror());


}

