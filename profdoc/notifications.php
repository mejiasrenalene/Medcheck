<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
$doctorId = intval($doctor['docid']);
$today = date('Y-m-d');

$notifications = [];
$upcomingResult = $database->query("SELECT appointment.appoid, patient.pname, appointment.appodate, schedule.scheduletime, schedule.title FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid INNER JOIN patient ON appointment.pid=patient.pid WHERE schedule.docid={$doctorId} AND appointment.appodate>='{$today}' ORDER BY appointment.appodate ASC, schedule.scheduletime ASC LIMIT 6");
if ($upcomingResult) {
    while ($row = $upcomingResult->fetch_assoc()) {
        $notifications[] = [
            'title' => 'Upcoming appointment with ' . $row['pname'],
            'message' => 'Scheduled for ' . $row['appodate'] . ' at ' . substr($row['scheduletime'], 0, 5) . ' - ' . ($row['title'] ?: 'Consultation'),
            'type' => 'success',
        ];
    }
}

$missedResult = $database->query("SELECT appointment.appoid, patient.pname, appointment.appodate, schedule.scheduletime, schedule.title FROM appointment INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid INNER JOIN patient ON appointment.pid=patient.pid WHERE schedule.docid={$doctorId} AND appointment.appodate < '{$today}' ORDER BY appointment.appodate DESC LIMIT 4");
if ($missedResult) {
    while ($row = $missedResult->fetch_assoc()) {
        $notifications[] = [
            'title' => 'Missed appointment with ' . $row['pname'],
            'message' => 'Missed on ' . $row['appodate'] . ' at ' . substr($row['scheduletime'], 0, 5),
            'type' => 'warning',
        ];
    }
}

if (empty($notifications)) {
    $notifications[] = [
        'title' => 'No notifications yet',
        'message' => 'Your doctor dashboard is up to date.',
        'type' => 'neutral',
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profdoc-notifications { padding: 24px; }
        .profdoc-notifications .section-card { background: #fff; border-radius: 18px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); padding: 24px; margin-bottom: 22px; }
        .profdoc-notifications .notification-item { border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; margin-bottom: 14px; }
        .profdoc-notifications .notification-item h3 { margin: 0 0 8px; font-size: 17px; }
        .profdoc-notifications .notification-item p { margin: 0; color: #475569; }
        .profdoc-notifications .badge-success { color: #166534; }
        .profdoc-notifications .badge-warning { color: #a16207; }
        .profdoc-notifications .badge-neutral { color: #334155; }
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
    <div class="container profdoc-notifications">
        <?php renderProfdocMenu('notifications', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-bell"></i> Notifications</h1>
                    <p>Keep up with appointment updates, missed visits, and patient alerts.</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo date('F d, Y'); ?>
                </div>
            </div>
            <div class="dash-body" style="margin-top: 15px;">
                <div class="section-card">
                    <h2>Notifications</h2>
                <?php foreach ($notifications as $note): ?>
                    <div class="notification-item">
                        <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                        <p><?php echo htmlspecialchars($note['message']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
