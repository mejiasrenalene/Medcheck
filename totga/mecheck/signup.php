<?php
session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"]=$date;

if($_POST){
    $_SESSION["personal"]=array(
        'fname'=>$_POST['fname'],
        'lname'=>$_POST['lname'],
        'address'=>$_POST['address'],
        'nic'=>$_POST['nic'],
        'dob'=>$_POST['dob']
    );

    header("location: create-account.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedCheck Sign Up</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
<?php include 'css/signup.css'; ?>
</style>

</head>
<body>

<div class="container">

    <div class="card">

        <h1>MEDCHECK</h1>
        <p class="welcome">Create Your Account</p>

        <form method="POST">

            <div class="row">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
            </div>

            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="nic" placeholder="NIC Number" required>
            <input type="date" name="dob" required>

            <div class="buttons">
                <input type="reset" value="Reset" class="btn reset">
                <input type="submit" value="Next" class="btn submit">
            </div>

        </form>

        <p class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </p>

    </div>

</div>

</body>
</html>