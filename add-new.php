<?php
include("../connection.php");

if($_POST){

$name=$_POST['name'];
$nic=$_POST['nic'];
$spec=$_POST['spec'];
$email=$_POST['email'];
$tele=$_POST['tele'];
$password=$_POST['password'];

$check = $database->query("SELECT * FROM webuser WHERE email='$email'");

if($check->num_rows==0){

$database->query("INSERT INTO doctor(docemail,docname,docpassword,docnic,doctel,specialties)
VALUES('$email','$name','$password','$nic','$tele','$spec')");

$database->query("INSERT INTO webuser VALUES('$email','d')");

}

header("location: doctors.php");
}
?>