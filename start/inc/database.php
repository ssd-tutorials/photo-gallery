<?php
$hostname_conndb = "localhost";
$database_conndb = "gallery";
$username_conndb = "root";
$password_conndb = "password";
$conndb = mysql_pconnect($hostname_conndb, $username_conndb, $password_conndb) or trigger_error(mysql_error(),E_USER_ERROR);
?>