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

/* USER */
$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();

$userid= $userfetch["pid"];
$username=$userfetch["pname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings | Medcheck</title>

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
        <i class="fa-solid fa-user"></i> <?php echo $username; ?>
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
    <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a href="schedule.php"><i class="fa-solid fa-calendar-check"></i> Sessions</a>
    <a href="appointment.php"><i class="fa-solid fa-book"></i> My Bookings</a>
    <a class="active" href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>

    <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="page-header">
    <h1><i class="fa-solid fa-gear"></i> Account Settings</h1>
    <p>Manage your profile, privacy, and account security preferences</p>
</div>

<!-- PROFILE SUMMARY -->
<div class="card" style="display:flex;align-items:center;gap:20px;padding:25px;">
    
    <img src="../img/user.png" style="width:80px;border-radius:50%;border:3px solid #00bcd4;">

    <div>
        <h2 style="margin:0;"><?php echo $username; ?></h2>
        <p style="margin:5px 0;color:#777;">
            <i class="fa-solid fa-envelope"></i> <?php echo $useremail; ?>
        </p>
        <span style="background:#e7f0ff;color:#2d6cdf;padding:5px 10px;border-radius:20px;font-size:12px;">
            Verified Patient Account
        </span>
    </div>

</div>

<!-- SETTINGS GRID -->
<div class="grid">

    <!-- EDIT -->
    <a href="?action=edit&id=<?php echo $userid ?>">
    <div class="card stat-card">
        <div class="stat-top">
            <div class="stat-icon"><i class="fa-solid fa-user-pen"></i></div>
        </div>
        <h3>Edit Profile</h3>
        <p>Update your personal information</p>
    </div>
    </a>

    <!-- VIEW -->
    <a href="?action=view&id=<?php echo $userid ?>">
    <div class="card stat-card">
        <div class="stat-top">
            <div class="stat-icon"><i class="fa-solid fa-id-card"></i></div>
        </div>
        <h3>View Profile</h3>
        <p>Review your account details, personal information, and registered data.</p>
    </div>
    </a>

    <!-- SECURITY -->
    <div class="card stat-card">
        <div class="stat-top">
            <div class="stat-icon"><i class="fa-solid fa-shield-halved"></i></div>
        </div>
        <h3>Security</h3>
        <p>Manage password, login protection, and account safety settings.</p>
    </div>

</div>

<!-- DANGER ZONE -->
<div class="card" style="margin-top:25px;border-left:6px solid #e74c3c;padding:25px;">

    <h2 style="color:#e74c3c;margin:0;">
        <i class="fa-solid fa-triangle-exclamation"></i> Account Management
    </h2>

    <p style="color:#777;margin-top:8px;">
        You can temporarily deactivate your account or permanently delete it. Deleted data cannot be recovered.
    </p>

    <a href="?action=drop&id=<?php echo $userid ?>&name=<?php echo $username ?>">
        <button class="book-btn" style="background:#e74c3c;margin-top:10px;">
            <i class="fa-solid fa-sliders"></i> Manage Account
        </button>
    </a>

</div>

</div>
</div>

<?php

if($_GET){

$id=$_GET["id"];
$action=$_GET["action"];

/* VIEW */
if($action=='view'){

$row=$database->query("select * from patient where pid='$id'")->fetch_assoc();

echo '
<div class="popup">
    <div class="popup-content">

        <h2><i class="fa-solid fa-user"></i> Profile Overview</h2>

        <p><i class="fa-solid fa-user"></i> '.$row["pname"].'</p>
        <p><i class="fa-solid fa-envelope"></i> '.$row["pemail"].'</p>
        <p><i class="fa-solid fa-phone"></i> '.$row["ptel"].'</p>
        <p><i class="fa-solid fa-location-dot"></i> '.$row["paddress"].'</p>

        <a href="settings.php">
            <button class="book-btn">Close</button>
        </a>

    </div>
</div>';
}

/* EDIT */
elseif($action=='edit'){

$row=$database->query("select * from patient where pid='$id'")->fetch_assoc();

echo '
<div class="popup">
    <div class="popup-content">

        <h2><i class="fa-solid fa-user-pen"></i> Update Profile</h2>

        <form action="edit-user.php" method="POST">

            <input type="hidden" name="id00" value="'.$id.'">

            <input type="email" name="email" value="'.$row["pemail"].'" required><br><br>
            <input type="text" name="name" value="'.$row["pname"].'" required><br><br>
            <input type="text" name="nic" value="'.$row["pnic"].'" required><br><br>
            <input type="text" name="Tele" value="'.$row["ptel"].'" required><br><br>
            <input type="text" name="address" value="'.$row["paddress"].'" required><br><br>

            <input type="password" name="password" placeholder="New Password"><br><br>
            <input type="password" name="cpassword" placeholder="Confirm Password"><br><br>

            <button class="book-btn">
                Save Changes
            </button>

        </form>

        <a href="settings.php">
            <button class="book-btn">Cancel</button>
        </a>

    </div>
</div>';
}

/* ACCOUNT MANAGEMENT */
elseif($action=='drop'){

$name=$_GET["name"];

echo '
<div class="popup">
    <div class="popup-content">

        <h2 style="color:#e74c3c;">
            <i class="fa-solid fa-user-slash"></i> Account Options
        </h2>

        <p>Hello <b>'.$name.'</b>, please choose an action below:</p>

        <div style="display:flex;flex-direction:column;gap:10px;margin-top:15px;">

            <a href="deactivate-account.php?id='.$id.'">
                <button class="book-btn" style="background:#f39c12;">
                    Deactivate Account
                </button>
            </a>

            <a href="delete-account.php?id='.$id.'">
                <button class="book-btn" style="background:#e74c3c;">
                    Delete Account Permanently
                </button>
            </a>

            <a href="settings.php">
                <button class="book-btn">
                    Cancel
                </button>
            </a>

        </div>

    </div>
</div>';
}

}
?>

</body>
</html>