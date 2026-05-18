<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Actions</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profdoc-actions { padding: 24px; }
        .profdoc-actions .section-card { background: #fff; border-radius: 18px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); padding: 24px; margin-bottom: 22px; }
        .profdoc-actions .section-card h2 { font-size: 22px; margin-bottom: 18px; }
        .profdoc-actions .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .profdoc-actions .action-card { padding: 22px; border-radius: 18px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .profdoc-actions .action-card h3 { margin: 0 0 10px; font-size: 18px; }
        .profdoc-actions .action-card p { margin: 0 0 16px; color: #475569; }
        .profdoc-actions .action-card a { display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border-radius: 999px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; }
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
    <div class="container profdoc-actions">
        <?php renderProfdocMenu('actions', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-lightbulb"></i> Actions</h1>
                    <p>Quick access to your appointment workflow, patient list, notifications, and performance metrics.</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo date('F d, Y'); ?>
                </div>
            </div>
            <div class="dash-body" style="margin-top: 15px;">
                <div class="section-card">
                    <h2>Doctor Actions</h2>
                <div class="action-grid">
                    <div class="action-card">
                        <h3>Review Upcoming Appointments</h3>
                        <p>Open your schedule and confirm patient visits for the coming days.</p>
                        <a href="UpcomingAppointments.php">View Appointments</a>
                    </div>
                    <div class="action-card">
                        <h3>Open Patient List</h3>
                        <p>Manage your patient roster and follow up on recent visits.</p>
                        <a href="mypatients.php">View Patients</a>
                    </div>
                    <div class="action-card">
                        <h3>Check Quick Stats</h3>
                        <p>Review appointment performance, session volume and patient activity.</p>
                        <a href="QuickStats.php">View Metrics</a>
                    </div>
                    <div class="action-card">
                        <h3>Read Notifications</h3>
                        <p>See alerts for missed appointments and urgent follow-ups.</p>
                        <a href="notifications.php">View Notifications</a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
