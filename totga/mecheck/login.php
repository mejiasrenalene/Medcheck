<?php
session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"]=$date;

include("connection.php");

function ensureDoctorAccount($database) {
    $exists = $database->query("select * from webuser where email='doctor'");
    if ($exists->num_rows == 0) {
        $database->query("insert into webuser(email,usertype) values('doctor','d')");
    }
    $existsDoc = $database->query("select * from doctor where docemail='doctor'");
    if ($existsDoc->num_rows == 0) {
        $database->query("insert into doctor(docemail,docname,docpassword,docnic,doctel,specialties) values('doctor','Dr. Doctor','doctor','0000000000','0111111111',1)");
    }
}

if($_POST){

    $email=$_POST['useremail'];
    $password=$_POST['userpassword'];
    
    if ($email === 'doctor' && $password === 'doctor') {
        ensureDoctorAccount($database);
    }
    
    $error="";

    $result= $database->query("select * from webuser where email='$email'");
    if($result->num_rows==1){
        $utype=$result->fetch_assoc()['usertype'];

        if ($utype=='p'){
            $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
            if ($checker->num_rows==1){
                $_SESSION['user']=$email;
                $_SESSION['usertype']='p';
                header('location: patient/index.php');
            } else {
                $error="Wrong email or password";
            }

        } elseif($utype=='a'){
            $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
            if ($checker->num_rows==1){
                $_SESSION['user']=$email;
                $_SESSION['usertype']='a';
                header('location: admin/index.php');
            } else {
                $error="Wrong email or password";
            }

        } elseif($utype=='d'){
            $checker = $database->query("select * from doctor where docemail='$email' and docpassword='$password'");
            if ($checker->num_rows==1){
                $_SESSION['user']=$email;
                $_SESSION['usertype']='d';
                header('location: profdoc/dashboard.php');
            } else {
                $error="Wrong email or password";
            }
        }

    } else {
        $error="Account not found";
    }

} else {
    $error="";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MedCheck Login</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
<?php include 'css/login.css'; ?>
</style>

</head>

<body>

<div class="container">
    <div class="card">

        <!-- 🔥 BRAND -->
        <h1>MEDCHECK</h1>
        <p class="welcome">Welcome Back</p>

        <form method="POST">

            <input type="text" name="useremail" placeholder="Email Address or Username" required>
            <input type="password" name="userpassword" placeholder="Password" required>

            <?php if($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <button type="submit" class="btn">Login</button>

        </form>

        <p class="hint" style="margin-top:1rem;color:#475569;font-size:0.95rem;"><strong></strong> / <strong></strong> or <strong></strong> / <strong></strong>.</p>

        <p class="link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </p>

    </div>
</div>

</body>
</html>