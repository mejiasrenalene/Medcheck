<?php
session_start();

if(isset($_SESSION["user"])){
    if($_SESSION["user"]=="" || $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    } else {
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$username=$userfetch["pname"];

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sessions</title>

<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="topbar">
    <div class="logo"><i class="fa-solid fa-heart-pulse"></i> MEDCHECK</div>
    <div><i class="fa-solid fa-user"></i> <?php echo $username; ?></div>
</div>

<div class="container">

<div class="sidebar">

    <div class="profile">
        <img src="../img/user.png">
        <h3><?php echo $username; ?></h3>
        <p>Patient</p>
    </div>

    <a href="index.php"><i class="fa-solid fa-house"></i>Home</a>
    <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a class="active" href="schedule.php"><i class="fa-solid fa-calendar"></i> Sessions</a>
    <a href="appointment.php"><i class="fa-solid fa-book"></i> My Bookings</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>

</div>

<div class="main">

<div class="page-header">
    <h1><i class="fa-solid fa-calendar"></i> Available Sessions</h1>
    <p>Book appointments with verified doctors</p>
</div>

<div class="card">
<form method="post" class="search-form">
    <input type="search" name="search" placeholder="Search session...">
    <button><i class="fa-solid fa-magnifying-glass"></i> Search</button>
</form>
</div>

<?php

$sql = "SELECT * FROM schedule 
INNER JOIN doctor ON schedule.docid=doctor.docid 
WHERE schedule.scheduledate>='$today'
ORDER BY schedule.scheduledate ASC";

if($_POST && !empty($_POST["search"])){
    $k = $_POST["search"];
    $sql = "SELECT * FROM schedule 
    INNER JOIN doctor ON schedule.docid=doctor.docid 
    WHERE schedule.scheduledate>='$today'
    AND (doctor.docname LIKE '%$k%' OR schedule.title LIKE '%$k%' OR schedule.scheduledate LIKE '%$k%')
    ORDER BY schedule.scheduledate ASC";
}

$result = $database->query($sql);
?>

<div class="card">
    <h3>Sessions Found: <?php echo $result->num_rows; ?></h3>
</div>

<div class="session-grid">

<?php

if($result->num_rows==0){
    echo "<div class='card'>No Sessions Found</div>";
}else{

while($row=$result->fetch_assoc()){

$id = $row["scheduleid"];
$title = $row["title"];
$doc = $row["docname"];
$date = $row["scheduledate"];
$time = date("h:i A", strtotime($row["scheduletime"]));

$days = (strtotime($date)-strtotime($today))/86400;

if($date==$today){
    $class="today";
    $label="TODAY";
}elseif($days>0){
    $class="upcoming";
    $label="UPCOMING";
}else{
    $class="passed";
    $label="PASSED";
}

echo "

<div class='session-card'>

<div class='session-header'>
    <h3>$title</h3>
    <span class='status $class'>$label</span>
</div>

<p><b>Doctor:</b> $doc</p>
<p><b>Date:</b> $date</p>
<p><b>Time:</b> $time</p>

<a href='booking.php?id=$id'>
    <button class='book-btn'>Quick Book</button>
</a>

</div>

";
}
}
?>

</div>

</div>
</div>

</body>
</html>