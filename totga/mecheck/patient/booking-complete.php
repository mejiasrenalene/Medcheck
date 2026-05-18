<?php
session_start();
include("../connection.php");

// =======================
// SESSION CHECK
// =======================
if(!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'p'){
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// =======================
// GET USER ID
// =======================
$stmt = $database->prepare("SELECT pid FROM patient WHERE pemail=?");
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if(!$user){
    header("location: ../login.php?error=user-not-found");
    exit();
}

$userid = $user["pid"];

// =======================
// HANDLE BOOKING
// =======================
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $apponum = $_POST["apponum"] ?? "";
    $scheduleid = $_POST["scheduleid"] ?? "";
    $date = $_POST["date"] ?? "";

    // =======================
    // VALIDATION
    // =======================
    if(empty($apponum) || empty($scheduleid) || empty($date)){
        header("location: appointment.php?error=missing-fields");
        exit();
    }

    // =======================
    // CHECK DUPLICATE BOOKING
    // =======================
    $check = $database->prepare("SELECT * FROM appointment WHERE pid=? AND scheduleid=?");
    $check->bind_param("ii", $userid, $scheduleid);
    $check->execute();

    $resultCheck = $check->get_result();

    if($resultCheck->num_rows > 0){
        header("location: appointment.php?error=already-booked");
        exit();
    }

    // =======================
    // INSERT APPOINTMENT
    // =======================
    $stmt2 = $database->prepare(
        "INSERT INTO appointment(pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)"
    );
    $stmt2->bind_param("iiis", $userid, $apponum, $scheduleid, $date);

    if($stmt2->execute()){
        header("location: appointment.php?success=booked");
        exit();
    } else {
        header("location: appointment.php?error=failed");
        exit();
    }
}
?>