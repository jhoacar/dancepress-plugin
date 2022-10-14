<?php
global $wp;
$current_url = home_url(add_query_arg(array(), $wp->request));
 ?>
<a href="<?=$current_url?>">Portal Home</a> | <a href="<?=$current_url?>/?page=viewclass">View Classes</a> | <a href="<?=$current_url?>/?page=parentprofile">Profile</a> | <a href="<?=$current_url?>/?page=parentbillinghistory">Billing</a> | <a href="<?=$current_url?>/?page=ticketpurchases">Tickets</a>
