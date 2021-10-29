<?php

$_POST['username'] = 'aidan';
$_POST['password'] = "' OR ''='";

$query = "SELECT * FROM users WHERE user='{$_POST['username']}' AND   password='{$_POST['password']}'";
mysqli_query($query);

echo $query;
?>