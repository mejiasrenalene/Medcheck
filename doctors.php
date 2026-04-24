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

// USER
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$username=$userfetch["pname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctors - Medcheck</title>

<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="logo">
        <i class="fa-solid fa-heart-pulse"></i> MEDCHECK
    </div>
    <div class="user">
        <i class="fa-solid fa-user"></i>
        Welcome back, <?php echo $username; ?>
    </div>
</div>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="profile">
        <img src="../img/user.png">
        <h3><?php echo $username; ?></h3>
        <p>Patient</p>
    </div>

    <a href="index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a class="active" href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a href="schedule.php"><i class="fa-solid fa-calendar-check"></i> Sessions</a>
    <a href="appointment.php"><i class="fa-solid fa-book"></i> Bookings</a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>

    <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="page-header">
    <h1><i class="fa-solid fa-user-doctor"></i> Doctors</h1>
    <p>Find licensed medical professionals and book consultations instantly.</p>
</div>

<!-- SEARCH -->
<div class="card search-card">
    <form method="POST" class="search-form">
        <input type="search" name="search" placeholder="Search doctor name or email..." required>
        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
    </form>
</div>

<!-- TABLE -->
<div class="card table-card">

<h3><i class="fa-solid fa-hospital-user"></i> Available Doctors (<?php echo $database->query("SELECT * FROM doctor")->num_rows; ?>)</h3>

<table>
<tr>
    <th><i class="fa-solid fa-user-doctor"></i> Name</th>
    <th><i class="fa-solid fa-envelope"></i> Email</th>
    <th><i class="fa-solid fa-stethoscope"></i> Specialty</th>
    <th><i class="fa-solid fa-circle-info"></i> Action</th>
</tr>

<?php

if($_POST){
    $keyword=$_POST["search"];
    $sqlmain= "SELECT * FROM doctor WHERE docemail='$keyword' OR docname LIKE '%$keyword%'";
}else{
    $sqlmain= "SELECT * FROM doctor ORDER BY docid DESC";
}

$result= $database->query($sqlmain);

while($row=$result->fetch_assoc()){
    $docid=$row["docid"];
    $name=$row["docname"];
    $email=$row["docemail"];
    $spe=$row["specialties"];

    $sp = $database->query("SELECT sname FROM specialties WHERE id='$spe'");
    $spn=$sp->fetch_assoc();
    $specialty = $spn["sname"];

    echo "
    <tr>
        <td>$name</td>
        <td>$email</td>
        <td>$specialty</td>
        <td>
            <a href='?action=view&id=$docid'>
                <button class='view-btn'>
                    <i class='fa-solid fa-eye'></i> View
                </button>
            </a>
        </td>
    </tr>
    ";
}
?>

</table>

</div>

</div>
</div>

<?php
// POPUP
if(isset($_GET['action']) && $_GET['action']=='view'){

$id=$_GET['id'];

$doc = $database->query("SELECT * FROM doctor WHERE docid='$id'");
$d=$doc->fetch_assoc();

$spe=$d["specialties"];
$sp = $database->query("SELECT sname FROM specialties WHERE id='$spe'");
$spn=$sp->fetch_assoc();
?>

<div class="popup">
    <div class="popup-content">

        <h2><i class="fa-solid fa-user-doctor"></i> Doctor Profile</h2>

        <div class="popup-info">
            <p><i class="fa-solid fa-user"></i> <b>Name:</b> <?php echo $d['docname']; ?></p>
            <p><i class="fa-solid fa-envelope"></i> <b>Email:</b> <?php echo $d['docemail']; ?></p>
            <p><i class="fa-solid fa-stethoscope"></i> <b>Specialty:</b> <?php echo $spn['sname']; ?></p>
        </div>

        <p class="note">
        <?php
        if(strtolower($spn['sname'])=="pediatrics"){
            echo "<i class='fa-solid fa-baby'></i> Pediatric Specialist - Child healthcare & development";
        } elseif(strtolower($spn['sname'])=="cardiology"){
            echo "<i class='fa-solid fa-heart-pulse'></i> Cardiology Specialist - Heart & cardiovascular care";
        } else {
            echo "<i class='fa-solid fa-stethoscope'></i> Available for medical consultation";
        }
        ?>
        </p>

        <a href="doctors.php">
            <button class="close-btn">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </a>

    </div>
</div>

<?php } ?>

</body>
</html>