<?php

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'quiz-platform';

$data = mysqli_connect($host,$user,$password,$db);
if($data -> connect_error)
{
  die("connection error".$data->connect_error );
}
else{
  echo"";
}
?>