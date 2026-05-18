<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
$doctorId = intval($doctor['docid']);
$today = date('Y-m-d');

$upcoming = [];
$sql = "SELECT appointment.appoid, patient.pname, appointment.appodate, schedule.scheduletime, schedule.title FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid INNER JOIN patient ON appointment.pid=patient.pid WHERE schedule.docid={$doctorId} AND appointment.appodate>='{$today}' ORDER BY appointment.appodate ASC, schedule.scheduletime ASC";
$result = $database->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $upcoming[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profdoc-appointments { padding: 24px; }
        .profdoc-appointments .section-card { background: #fff; border-radius: 18px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); padding: 24px; margin-bottom: 22px; }
        .profdoc-appointments .section-card h2 { font-size: 22px; margin-bottom: 18px; }
        .profdoc-appointments .table-list { width: 100%; border-collapse: collapse; }
        .profdoc-appointments .table-list th, .profdoc-appointments .table-list td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; }
        .profdoc-appointments .table-list th { text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
        .profdoc-appointments .status-pill { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .profdoc-appointments .status-pending { background: #ffedd5; color: #c2410c; }
        .profdoc-appointments .status-confirmed { background: #d1fae5; color: #166534; }
        .profdoc-appointments .status-completed { background: #e0f2fe; color: #0369a1; }
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
    <div class="container profdoc-appointments">
        <?php renderProfdocMenu('appointments', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-calendar-days"></i> Upcoming Appointments</h1>
                    <p>Track your upcoming patient appointments and stay ahead of your schedule.</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo date('F d, Y'); ?>
                </div>
            </div>
            <div class="dash-body" style="margin-top: 15px;">
                <div class="section-card">
                    <h2>Upcoming Appointments</h2>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Appointment Type</th>
                            <th>Room / Clinic</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcoming)): ?>
                            <tr><td colspan="6" style="padding: 18px; text-align: center; color: #475569;">No upcoming appointments found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($upcoming as $appt): ?>
                                <?php $status = statusLabel($appt['appodate'], $today); ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appt['pname']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['appodate']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($appt['scheduletime'], 0, 5)); ?></td>
                                    <td><?php echo htmlspecialchars($appt['title'] ?: 'Consultation'); ?></td>
                                    <td>Clinic A</td>
                                    <td><span class="status-pill <?php echo $status === 'Confirmed' ? 'status-confirmed' : ($status === 'Pending' ? 'status-pending' : 'status-completed'); ?>"><?php echo $status; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
