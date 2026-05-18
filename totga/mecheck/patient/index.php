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
$stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userfetch=$stmt->get_result()->fetch_assoc();

$username=$userfetch["pname"];

// STATS
$doctorrow = $database->query("SELECT * FROM doctor");
$patientrow = $database->query("SELECT * FROM patient");
$appointmentrow = $database->query("SELECT * FROM appointment");
$schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate=CURDATE()");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medcheck Dashboard</title>

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
        Welcome, <?php echo $username; ?>
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

    <a class="active" href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a href="schedule.php"><i class="fa-solid fa-calendar-check"></i> Sessions</a>
    <a href="appointment.php"><i class="fa-solid fa-book"></i> Bookings</a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>

    <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER (ONLY CHANGED PART) -->
<div class="card welcome">
    <h1>Dashboard</h1>
    <p>System Overview & Activity Summary</p>
</div>

<!-- STATS -->
<div class="grid">

    <div class="card stat-card">
        <div class="stat-top">
            <i class="fa-solid fa-user-doctor stat-icon"></i>
            <h2><?php echo ($doctorrow ? $doctorrow->num_rows : 0); ?></h2>
        </div>
        <p>Doctors</p>
    </div>

    <div class="card stat-card">
        <div class="stat-top">
            <i class="fa-solid fa-users stat-icon"></i>
            <h2><?php echo ($patientrow ? $patientrow->num_rows : 0); ?></h2>
        </div>
        <p>Patients</p>
    </div>

    <div class="card stat-card">
        <div class="stat-top">
            <i class="fa-solid fa-calendar-check stat-icon"></i>
            <h2><?php echo ($appointmentrow ? $appointmentrow->num_rows : 0); ?></h2>
        </div>
        <p>Bookings</p>
    </div>

    <div class="card stat-card">
        <div class="stat-top">
            <i class="fa-solid fa-clock stat-icon"></i>
            <h2><?php echo ($schedulerow ? $schedulerow->num_rows : 0); ?></h2>
        </div>
        <p>Today Sessions</p>
    </div>

</div>

</div>
</div>

<!-- JS -->
<script>
document.querySelectorAll('.stat-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('clicked'));
        card.classList.add('clicked');
    });
});
</script>

</body>
</html>