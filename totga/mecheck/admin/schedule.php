<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
        exit();
    }
    $username = "Administrator";
}else{
    header("location: ../login.php");
    exit();
}

//import database
include("../connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
    <title>Schedule</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:18px 24px;
            background:#fff;
            border-radius:20px;
            box-shadow:0 8px 30px rgba(15,54,86,0.08);
            margin-bottom:20px;
        }
        .topbar .logo{
            display:flex;
            align-items:center;
            gap:12px;
            font-size:18px;
            font-weight:700;
            color:var(--primarycolor);
        }
        .topbar .user{
            display:flex;
            align-items:center;
            gap:10px;
            color:#444;
            font-weight:600;
        }
        .sidebar{
            width:260px;
            background:#fff;
            padding:24px;
            border-radius:24px;
            box-shadow:0 10px 40px rgba(15,54,86,0.08);
            display:flex;
            flex-direction:column;
            gap:16px;
        }
        .sidebar .profile{
            text-align:center;
            padding-bottom:16px;
            border-bottom:1px solid #eef3fb;
        }
        .sidebar .profile img{
            width:86px;
            border-radius:50%;
        }
        .sidebar .profile h3{
            margin:12px 0 4px;
        }
        .sidebar .profile p{
            margin:0;
            color:#777;
            font-size:14px;
        }
        .sidebar a{
            display:flex;
            align-items:center;
            gap:12px;
            padding:14px 18px;
            border-radius:16px;
            color:#344050;
            font-weight:600;
            text-decoration:none;
            transition:0.3s;
        }
        .sidebar a:hover,
        .sidebar a.active{
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            color:#fff;
        }
        .sidebar a.logout{
            margin-top:16px;
            background:#f8fafd;
        }
        .sidebar a.logout:hover{
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            color:#fff;
        }
        .sidebar a i{
            width:22px;
            text-align:center;
        }
        .dash-body{
            flex:1;
            padding:0 28px 30px 28px;
        }
        .card.welcome{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:26px 28px;
            background:linear-gradient(135deg, #4facfe, #00c6ff);
            color:#fff;
            border-radius:28px;
            box-shadow:0 18px 40px rgba(15,54,86,0.15);
            margin-bottom:24px;
        }
        .card.welcome h1{
            margin:0;
            font-size:28px;
        }
        .card.welcome p{
            margin:8px 0 0;
            opacity:0.95;
            font-size:15px;
        }
        .welcome-info .date{
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-size:14px;
            color:#eef7ff;
            background:rgba(255,255,255,0.15);
            padding:10px 16px;
            border-radius:16px;
        }
        .card.table-card{
            padding:24px;
            border-radius:24px;
            background:#fff;
            box-shadow:0 10px 30px rgba(15,54,86,0.08);
            margin-bottom:24px;
        }
        .button-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            flex-wrap:wrap;
            margin-bottom:22px;
        }
        .button-row .title{
            font-size:20px;
            font-weight:700;
            color:#1f325d;
        }
        .btn.button-icon i{
            margin-right:8px;
        }
        .filter-container{
            border: 1px solid #eef3fb;
            padding: 18px;
            border-radius: 18px;
            display:flex;
            align-items:center;
            gap:16px;
            background:#f8fbff;
            margin-bottom:20px;
        }
        .filter-container td{
            border:none;
        }
        .filter-container label{
            font-size:14px;
            color:#556a89;
            font-weight:600;
        }
        .filter-container .input-text, .filter-container .box{
            width:100%;
            min-width:180px;
        }
        .sub-table{
            border: 1px solid #ebebeb;
            border-radius: 18px;
            overflow:hidden;
        }
        .table-headin{
            font-size:15px;
            font-weight:700;
            padding:16px;
            border-bottom: 2px solid #eef3fb;
            background: #f4fbff;
            color: #0f4db1;
            text-align:left;
        }
        .sub-table tbody tr:hover{
            background:#f4faff;
        }
        .sub-table td{
            padding:16px;
            border-bottom:1px solid #f1f7ff;
        }
        .btn-primary-soft{
            background-color:#eff7ff;
            color:#0a76d8;
            border:none;
        }
        .btn-primary-soft:hover{
            background-color:#4facfe;
            color:#fff;
        }
        .btn-filter{
            width:100%;
            min-width:120px;
            background:#4facfe;
            color:#fff;
            border:none;
        }
        .btn-filter:hover{
            background:#00c6ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <img src="../img/user.png" alt="">
                <h3><?php echo $username; ?></h3>
                <p>Administrator</p>
            </div>
            <a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
            <a class="active" href="schedule.php"><i class="fa-solid fa-calendar-days"></i> Schedule</a>
            <a href="appointment.php"><i class="fa-solid fa-calendar-check"></i> Appointment</a>
            <a href="patient.php"><i class="fa-solid fa-users"></i> Patients</a>
            <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
        <div class="dash-body">
            <div class="topbar">
                <div class="logo"><i class="fa-solid fa-heart-pulse"></i> MEDCHECK</div>
                <div class="user"><i class="fa-solid fa-user-shield"></i> Administrator</div>
            </div>
            <div class="card welcome">
                <div>
                    <h1><i class="fa-solid fa-calendar-days"></i> Schedule Manager</h1>
                    <p>Manage active sessions and appointments in one place.</p>
                </div>
                <div class="welcome-info">
                    <span class="date"><i class="fa-solid fa-calendar"></i> <?php date_default_timezone_set('Asia/Kolkata'); echo date('F d, Y'); ?></span>
                </div>
            </div>
            <div class="card table-card">
                <div class="button-row">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <a href="schedule.php" class="non-style-link"><button class="btn btn-primary-soft"><i class="fa-solid fa-arrow-left"></i> Back</button></a>
                        <span class="title">All Sessions (<?php $list110 = $database->query("select  * from  schedule;"); echo $list110->num_rows; ?>)</span>
                    </div>
                    <a href="?action=add-session&id=none&error=0" class="non-style-link"><button class="btn btn-primary button-icon"><i class="fa-solid fa-plus"></i> Add a Session</button></a>
                </div>
                <form action="" method="post" style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;background:#f8fbff;border:1px solid #eef3fb;border-radius:18px;padding:18px;margin-bottom:24px;">
                    <label style="font-size:14px;color:#556a89;font-weight:600;min-width:70px;">Date</label>
                    <input type="date" name="sheduledate" id="date" class="input-text" style="width:180px;">
                    <label style="font-size:14px;color:#556a89;font-weight:600;min-width:80px;">Doctor</label>
                    <select name="docid" class="box" style="min-width:220px;height:42px;border-radius:12px;padding:0 12px;">
                        <option value="" disabled selected hidden>Choose Doctor Name</option>
                        <?php 
                            $list11 = $database->query("select  * from  doctor order by docname asc;");
                            for ($y=0;$y<$list11->num_rows;$y++){
                                $row00=$list11->fetch_assoc();
                                $sn=$row00["docname"];
                                $id00=$row00["docid"];
                                echo "<option value='$id00'>$sn</option>";
                            };
                        ?>
                    </select>
                    <button type="submit" name="filter" class="btn btn-filter">Filter</button>
                </form>
               
                <?php
                    if($_POST){
                        //print_r($_POST);
                        $sqlpt1="";
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate=$_POST["sheduledate"];
                            $sqlpt1=" schedule.scheduledate='$sheduledate' ";
                        }


                        $sqlpt2="";
                        if(!empty($_POST["docid"])){
                            $docid=$_POST["docid"];
                            $sqlpt2=" doctor.docid=$docid ";
                        }
                        //echo $sqlpt2;
                        //echo $sqlpt1;
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid ";
                        $sqllist=array($sqlpt1,$sqlpt2);
                        $sqlkeywords=array(" where "," and ");
                        $key2=0;
                        foreach($sqllist as $key){

                            if(!empty($key)){
                                $sqlmain.=$sqlkeywords[$key2].$key;
                                $key2++;
                            };
                        };
                        //echo $sqlmain;

                        
                        
                        //
                    }else{
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate desc";

                    }

                ?>
                <div class="abc scroll">
                    <table width="100%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">Session Title</th>
                                <th class="table-headin">Doctor</th>
                                <th class="table-headin">Scheduled Date & Time</th>
                                <th class="table-headin">Max num that can be booked</th>
                                <th class="table-headin">Events</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                $result= $database->query($sqlmain);
                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="5">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-top:10px;">&nbsp; Show all Sessions &nbsp;</button></a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                }
                                else{
                                for ( $x=0; $x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop=$row["nop"];
                                    echo '<tr>
                                        <td>'.substr($title,0,30).'</td>
                                        <td>'.substr($docname,0,20).'</td>
                                        <td style="text-align:center;">'.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'</td>
                                        <td style="text-align:center;">'.$nop.'</td>
                                        <td>
                                        <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                                        <a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button class="btn-primary-soft btn button-icon" style="padding:10px 20px;">View</button></a>
                                        <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button class="btn-primary-soft btn button-icon" style="padding:10px 20px;">Remove</button></a>
                                        </div>
                                        </td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='add-session'){

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    
                    
                        <a class="close" href="schedule.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                        <div class="abc">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                                <td class="label-td" colspan="2">'.
                                   ""
                                
                                .'</td>
                            </tr>

                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session.</p><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                <form action="add-session.php" method="POST" class="add-new-form">
                                    <label for="title" class="form-label">Session Title : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br>
                                </td>
                            </tr>
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="docid" class="form-label">Select Doctor: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="docid" id="" class="box" >
                                    <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>';
                                        
        
                                        $list11 = $database->query("select  * from  doctor order by docname asc;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $sn=$row00["docname"];
                                            $id00=$row00["docid"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
        
        
        
                                        
                        echo     '       </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nop" class="form-label">Number of Patients/Appointment Numbers : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="number" name="nop" class="input-text" min="0"  placeholder="The final appointment number for this session depends on this number" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="date" class="form-label">Session Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="date" name="date" class="input-text" min="'.date('Y-m-d').'" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="time" class="form-label">Schedule Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="time" name="time" class="input-text" placeholder="Time" required><br>
                                </td>
                            </tr>
                           
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                
                                    <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
                                </td>
                
                            </tr>
                           
                            </form>
                            </tr>
                        </table>
                        </div>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
        }elseif($action=='session-added'){
            $titleget=$_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    <br><br>
                        <h2>Session Placed.</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                        '.substr($titleget,0,40).' was scheduled.<br><br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        
                        <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            You want to delete this record<br>('.substr($nameget,0,40).').
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-session.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            '; 
        }elseif($action=='view'){
            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  where  schedule.scheduleid=$id";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $docname=$row["docname"];
            $scheduleid=$row["scheduleid"];
            $title=$row["title"];
            $scheduledate=$row["scheduledate"];
            $scheduletime=$row["scheduletime"];
            
           
            $nop=$row['nop'];


            $sqlmain12= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.scheduleid=$id;";
            $result12= $database->query($sqlmain12);
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup" style="width: 70%;">
                    <center>
                        <h2></h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            
                            
                        </div>
                        <div class="abc scroll" style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Session Title: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$title.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Doctor of this session: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$docname.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">Scheduled Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$scheduledate.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Scheduled Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$scheduletime.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label"><b>Patients that Already registerd for this session:</b> ('.$result12->num_rows."/".$nop.')</label>
                                    <br><br>
                                </td>
                            </tr>

                            
                            <tr>
                            <td colspan="4">
                                <center>
                                 <div class="abc scroll">
                                 <table width="100%" class="sub-table scrolldown" border="0">
                                 <thead>
                                 <tr>   
                                        <th class="table-headin">
                                             Patient ID
                                         </th>
                                         <th class="table-headin">
                                             Patient name
                                         </th>
                                         <th class="table-headin">
                                             
                                             Appointment number
                                             
                                         </th>
                                        
                                         
                                         <th class="table-headin">
                                             Patient Telephone
                                         </th>
                                         
                                 </thead>
                                 <tbody>';
                                 
                
                
                                         
                                         $result= $database->query($sqlmain12);
                
                                         if($result->num_rows==0){
                                             echo '<tr>
                                             <td colspan="7">
                                             <br><br><br><br>
                                             <center>
                                             <img src="../img/notfound.svg" width="25%">
                                             
                                             <br>
                                             <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                             <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
                                             </a>
                                             </center>
                                             <br><br><br><br>
                                             </td>
                                             </tr>';
                                             
                                         }
                                         else{
                                         for ( $x=0; $x<$result->num_rows;$x++){
                                             $row=$result->fetch_assoc();
                                             $apponum=$row["apponum"];
                                             $pid=$row["pid"];
                                             $pname=$row["pname"];
                                             $ptel=$row["ptel"];
                                             
                                             echo '<tr style="text-align:center;">
                                                <td>
                                                '.substr($pid,0,15).'
                                                </td>
                                                 <td style="font-weight:600;padding:25px">'.
                                                 
                                                 substr($pname,0,25)
                                                 .'</td >
                                                 <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                 '.$apponum.'
                                                 
                                                 </td>
                                                 <td>
                                                 '.substr($ptel,0,25).'
                                                 </td>
                                                 
                                                 
                
                                                 
                                             </tr>';
                                             
                                         }
                                     }
                                          
                                     
                
                                    echo '</tbody>
                
                                 </table>
                                 </div>
                                 </center>
                            </td> 
                         </tr>

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';  
    }
}
        
    ?>
    </div>

</body>
</html>