<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
$doctorId = intval($doctor['docid']);
$specialization = getDoctorSpecialization($database, $doctor['specialties']);

$today = date('Y-m-d');
$todayFormatted = date('F j, Y');
$todaySchedule = [];
$scheduleResult = $database->query("SELECT title, scheduletime, scheduledate FROM schedule WHERE docid={$doctorId} AND scheduledate='{$today}' ORDER BY scheduletime ASC");
if ($scheduleResult) {
    while ($row = $scheduleResult->fetch_assoc()) {
        $todaySchedule[] = $row;
    }
}

$totalAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId}");
$todaysAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate='{$today}'");
$completedAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate<'{$today}'");
$pendingAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate>='{$today}'");
$activePatients = countRows($database, "SELECT DISTINCT appointment.pid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId}");

$availability = 'Available';
if (count($todaySchedule) > 0) {
    $availability = count($todaySchedule) >= 4 ? 'Busy' : 'Available';
}

$upcomingList = [];
$upcomingResult = $database->query("SELECT appointment.appoid, patient.pname, schedule.title, appointment.appodate, schedule.scheduletime FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid INNER JOIN patient ON appointment.pid=patient.pid WHERE schedule.docid={$doctorId} AND appointment.appodate>='{$today}' ORDER BY appointment.appodate ASC, schedule.scheduletime ASC LIMIT 5");
if ($upcomingResult) {
    while ($row = $upcomingResult->fetch_assoc()) {
        $upcomingList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f4f9ff;
            color: #1f2937;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 20px;
            color: #0f4cd9;
        }
        .topbar .user {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-weight: 600;
        }
        .container {
            display: flex;
            gap: 24px;
            padding: 24px;
            min-height: calc(100vh - 82px);
        }
        .main {
            flex: 1;
        }
        .card.welcome {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 24px;
            background: linear-gradient(135deg, #3bb6ff 0%, #27a1f9 100%);
            color: #ffffff;
            border-radius: 28px;
            padding: 32px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
        }
        .welcome-content h1 {
            margin: 0 0 10px;
            font-size: 36px;
        }
        .welcome-content p {
            margin: 0;
            opacity: 0.9;
            font-size: 15px;
        }
        .welcome-info {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 16px 22px;
            font-weight: 700;
            font-size: 14px;
            color: #ffffff;
        }
        .show-all-btn {
            background: linear-gradient(135deg, #4facfe, #00c6ff);
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
        }
        .show-all-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }
        .panel-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }
        .panel-header h2 {
            margin: 0;
            font-size: 22px;
            color: #0f172a;
        }
        .panel-body {
            margin-top: 18px;
        }
        .panel-body table {
            width: 100%;
            border-collapse: collapse;
        }
        .panel-body th,
        .panel-body td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .panel-body th {
            text-align: left;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .panel-body td {
            color: #334155;
            font-size: 14px;
        }
        .section-title {
            margin: 24px 0 14px;
        }
        .section-title h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            font-size: 22px;
            color: #0f172a;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .stat-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }
        .stat-icon {
            font-size: 18px;
            background: #eaf5ff;
            color: #0f72d9;
            padding: 10px;
            border-radius: 12px;
        }
        .stat-card h2 {
            margin: 0;
            font-size: 34px;
            color: #0f172a;
        }
        .stat-card p {
            margin: 0;
            color: #64748b;
            font-size: 15px;
        }
        @media (max-width: 1120px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 760px) {
            .container {
                flex-direction: column;
                padding: 16px;
            }
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <i class="fa-solid fa-heart-pulse"></i> MEDCHECK
        </div>
        <div class="user">
            <i class="fa-solid fa-user-doctor"></i>
            <?php echo htmlspecialchars($doctorName); ?>
        </div>
    </div>
    <div class="container">
        <?php renderProfdocMenu('dashboard', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-chart-line"></i> Dashboard</h1>
                    <p>System Overview and Activity Summary</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo $todayFormatted; ?>
                </div>
            </div>

            <div class="grid">
                <div class="card stat-card">
                    <div class="stat-top">
                        <i class="fa-solid fa-stethoscope stat-icon"></i>
                        <h2><?php echo countRows($database, "SELECT docid FROM doctor"); ?></h2>
                    </div>
                    <p>Doctors</p>
                </div>
                <div class="card stat-card">
                    <div class="stat-top">
                        <i class="fa-solid fa-user-injured stat-icon"></i>
                        <h2><?php echo countRows($database, "SELECT pid FROM patient"); ?></h2>
                    </div>
                    <p>Patients</p>
                </div>
                <div class="card stat-card">
                    <div class="stat-top">
                        <i class="fa-solid fa-calendar-check stat-icon"></i>
                        <h2><?php echo countRows($database, "SELECT appoid FROM appointment"); ?></h2>
                    </div>
                    <p>Appointments</p>
                </div>
                <div class="card stat-card">
                    <div class="stat-top">
                        <i class="fa-solid fa-clock stat-icon"></i>
                        <h2><?php echo count($todaySchedule); ?></h2>
                    </div>
                    <p>Today Sessions</p>
                </div>
            </div>

            <div class="section-title">
                <h2><i class="fa-solid fa-chart-pie"></i> System Insights</h2>
            </div>
            <div class="grid">
                <div class="card stat-card">
                    <h2><?php echo $pendingAppointments; ?></h2>
                    <p>Pending</p>
                </div>
                <div class="card stat-card">
                    <h2><?php echo $completedAppointments; ?></h2>
                    <p>Completed</p>
                </div>
                <div class="card stat-card">
                    <h2>0</h2>
                    <p>Cancelled</p>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin:20px 0;">
                <a href="UpcomingAppointments.php" class="show-all-btn">
                    <i class="fa-solid fa-calendar-check"></i>
                    Show All Appointments
                </a>
            </div>

            <div class="card panel-card">
                <div class="panel-header">
                    <h2>Upcoming Appointments</h2>
                    <a href="UpcomingAppointments.php" class="show-all-btn">Show All Appointments</a>
                </div>
                <div class="panel-body">
                    <?php if (empty($upcomingList)): ?>
                        <p>No upcoming appointments.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; foreach ($upcomingList as $entry): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($entry['pname']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($entry['appodate'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($entry['scheduletime'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
