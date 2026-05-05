<?php
include("../connection.php");

$id=$_GET['id'];
$doc = $database->query("SELECT * FROM doctor WHERE docid=$id")->fetch_assoc();

if($_POST){

$name=$_POST['name'];
$email=$_POST['email'];
$nic=$_POST['nic'];
$tele=$_POST['tele'];
$spec=$_POST['spec'];

$database->query("UPDATE doctor SET 
docname='$name',
docemail='$email',
docnic='$nic',
doctel='$tele',
specialties='$spec'
WHERE docid=$id");

header("location: doctors.php");
}
?>

<form method="POST">
<input name="name" value="<?php echo $doc['docname']; ?>">
<input name="email" value="<?php echo $doc['docemail']; ?>">
<input name="nic" value="<?php echo $doc['docnic']; ?>">
<input name="tele" value="<?php echo $doc['doctel']; ?>">
<input name="spec" value="<?php echo $doc['specialties']; ?>">

<button>Update</button>
</form>