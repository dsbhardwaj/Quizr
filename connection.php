<?php

$host = 'sql308.infinityfree.com';
$user = 'if0_42085094';
$password = 'YOUR_PASSWORD_HERE';
$db = 'if0_42085094_quizr';

$data = mysqli_connect($host,$user,$password,$db);
if($data -> connect_error)
{
  die("connection error".$data->connect_error );
}
else{
  echo"";
}
?>