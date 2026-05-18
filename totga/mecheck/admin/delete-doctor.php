<?php
include("../connection.php");

$id=$_GET['id'];

$doc = $database->query("SELECT * FROM doctor WHERE docid=$id")->fetch_assoc();
$email=$doc['docemail'];

$database->query("DELETE FROM doctor WHERE docid=$id");
$database->query("DELETE FROM webuser WHERE email='$email'");

header("location: doctors.php");
?>