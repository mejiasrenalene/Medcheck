<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
$doctorId = intval($doctor['docid']);
$today = date('Y-m-d');

$totalAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId}");
$todaysAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate='{$today}'");
$pendingAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate>='{$today}'");
$completedAppointments = countRows($database, "SELECT appointment.appoid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} AND appointment.appodate<'{$today}'");
$activePatients = countRows($database, "SELECT DISTINCT appointment.pid FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId}");
$totalSessions = countRows($database, "SELECT scheduleid FROM schedule WHERE docid={$doctorId}");

$topPatients = [];
$patientResult = $database->query("SELECT patient.pname, patient.pemail, COUNT(appointment.appoid) AS visits FROM appointment INNER JOIN patient ON appointment.pid=patient.pid INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid WHERE schedule.docid={$doctorId} GROUP BY patient.pid ORDER BY visits DESC LIMIT 4");
if ($patientResult) {
    while ($row = $patientResult->fetch_assoc()) {
        $topPatients[] = $row;
    }
}
?>SSS
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Stats</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profdoc-stats { padding: 24px; }
        .profdoc-stats .section-card { background: #fff; border-radius: 18px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); padding: 24px; margin-bottom: 22px; }
        .profdoc-stats .section-card h2 { font-size: 22px; margin-bottom: 18px; }
        .profdoc-stats .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
        .profdoc-stats .stats-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; }
        .profdoc-stats .stats-card .value { font-size: 32px; font-weight: 700; margin-bottom: 8px; }
        .profdoc-stats .stats-card .label { color: #475569; }
        .profdoc-stats .top-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .profdoc-stats .top-table th, .profdoc-stats .top-table td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; }
        .profdoc-stats .top-table th { text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
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
    <div class="container profdoc-stats">
        <?php renderProfdocMenu('quickstats', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-chart-line"></i> Quick Stats</h1>
                    <p>Review your appointment performance, session volume, and patient activity in one place.</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo date('F d, Y'); ?>
                </div>
            </div>
            <div class="dash-body" style="margin-top: 15px;">
                <div class="section-card">
                    <h2>Quick Performance Metrics</h2>
                <div class="stats-grid">
                    <div class="stats-card"><div class="value"><?php echo $totalAppointments; ?></div><div class="label">Total Appointments</div></div>
                    <div class="stats-card"><div class="value"><?php echo $todaysAppointments; ?></div><div class="label">Today’s Appointments</div></div>
                    <div class="stats-card"><div class="value"><?php echo $activePatients; ?></div><div class="label">Active Patients</div></div>
                    <div class="stats-card"><div class="value"><?php echo $totalSessions; ?></div><div class="label">Scheduled Sessions</div></div>
                </div>
            </div>

            <div class="section-card">
                <h2>Progress Summary</h2>
                <div class="stats-grid">
                    <div class="stats-card"><div class="value"><?php echo $pendingAppointments; ?></div><div class="label">Pending Appointments</div></div>
                    <div class="stats-card"><div class="value"><?php echo $completedAppointments; ?></div><div class="label">Completed Appointments</div></div>
                </div>
            </div>

            <div class="section-card">
                <h2>Top Patients</h2>
                <table class="top-table">
                    <thead><tr><th>Patient</th><th>Email</th><th>Visits</th></tr></thead>
                    <tbody>
                        <?php if (empty($topPatients)): ?>
                            <tr><td colspan="3">No patient activity available yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($topPatients as $patient): ?>
                                <tr><td><?php echo htmlspecialchars($patient['pname']); ?></td><td><?php echo htmlspecialchars($patient['pemail']); ?></td><td><?php echo htmlspecialchars($patient['visits']); ?></td></tr>
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
