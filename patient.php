<?php
session_start();

if(isset($_SESSION["user"])){
    if($_SESSION["user"]=="" || $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    } else {
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");

// USER
$stmt = $database->prepare("select * from patient where pemail=?");
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userfetch=$stmt->get_result()->fetch_assoc();

$username=$userfetch["pname"];

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Medcheck | Sessions</title>

<link rel="stylesheet" href="../css/index.css">

<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="logo">
        <i class="fa-solid fa-heart-pulse"></i> MEDCHECK
    </div>
    <div class="user">
        <i class="fa-solid fa-user-doctor"></i>
        Welcome back, <?php echo $username; ?>
    </div>
</div>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="profile">
        <img src="../img/user.png">
        <h3><?php echo $username; ?></h3>
        <p><i class="fa-solid fa-user"></i> Patient</p>
    </div>

    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a class="active" href="schedule.php"><i class="fa-solid fa-calendar-check"></i> Sessions</a>
    <a href="appointment.php"><i class="fa-solid fa-book"></i> Bookings</a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>

    <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="card welcome">
    <h1><i class="fa-solid fa-calendar-days"></i> Available Sessions</h1>
    <p>Book appointments with verified doctors easily</p>
</div>

<!-- SEARCH -->
<div class="card">
<form method="post">
    <input type="search" name="search" placeholder="Search doctor, title or date...">
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
</form>
</div>

<?php

$sqlmain= "select * from schedule 
inner join doctor on schedule.docid=doctor.docid 
where schedule.scheduledate>='$today'
order by schedule.scheduledate asc";

$keyword="";

if($_POST && !empty($_POST["search"])){
    $keyword=$_POST["search"];

    $sqlmain= "select * from schedule 
    inner join doctor on schedule.docid=doctor.docid 
    where schedule.scheduledate>='$today'
    and (
        doctor.docname like '%$keyword%' 
        or schedule.title like '%$keyword%' 
        or schedule.scheduledate like '%$keyword%'
    )
    order by schedule.scheduledate asc";
}

$result=$database->query($sqlmain);
?>

<!-- INFO CARD -->
<div class="card">
    <h3>
        <i class="fa-solid fa-list"></i>
        Sessions Found: <?php echo $result->num_rows; ?>
    </h3>

    <?php if($keyword!=""){ ?>
        <p>Search keyword: <b><?php echo $keyword; ?></b></p>
    <?php } ?>
</div>

<!-- SESSION GRID -->
<div class="session-grid">

<?php

if($result->num_rows==0){
    echo "
    <div class='card'>
        <h3><i class='fa-solid fa-triangle-exclamation'></i> No Sessions Found</h3>
        <p>Try different keywords or check back later.</p>
    </div>
    ";
}else{

    while($row=$result->fetch_assoc()){

        $id=$row["scheduleid"];
        $title=$row["title"];
        $doc=$row["docname"];
        $date=$row["scheduledate"];
        $time=$row["scheduletime"];

        $timeFormatted = date("h:i A", strtotime($time));

        // STATUS SYSTEM
        $daysLeft = (strtotime($date) - strtotime($today)) / 86400;

        if($date == $today){
            $status = "🟢 TODAY";
        } elseif($daysLeft > 0){
            $status = "🔵 UPCOMING";
        } else {
            $status = "⚫ PASSED";
        }

        echo "

        <div class='session-card'>

            <div class='session-header'>
                <h3><i class='fa-solid fa-stethoscope'></i> $title</h3>
                <span class='status'>$status</span>
            </div>

            <p><i class='fa-solid fa-user-doctor'></i> $doc</p>
            <p><i class='fa-solid fa-calendar'></i> $date</p>
            <p><i class='fa-solid fa-clock'></i> $timeFormatted</p>

            <div class='session-footer'>
                <a href='booking.php?id=$id'>
                    <button class='book-btn'>
                        <i class='fa-solid fa-calendar-plus'></i>
                        Quick Book
                    </button>
                </a>
            </div>

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