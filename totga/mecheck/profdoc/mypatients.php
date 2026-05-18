<?php
include("common.php");
$doctor = requireDoctor();
$doctorName = $doctor['docname'] ?: 'Doctor';
$useremail = $_SESSION['user'];
$doctorId = intval($doctor['docid']);
$today = date('Y-m-d');

$search = trim($_GET['search'] ?? '');
$searchFilter = '';
if ($search !== '') {
    $searchValue = $database->real_escape_string($search);
    $searchFilter = " AND (patient.pname LIKE '%{$searchValue}%' OR patient.pemail LIKE '%{$searchValue}%')";
}

$sql = "SELECT patient.pid, patient.pname, patient.pemail, patient.ptel, ";
$sql .= "MAX(appointment.appodate) AS last_visit, ";
$sql .= "MIN(CASE WHEN appointment.appodate >= '{$today}' THEN appointment.appodate END) AS next_appointment ";
$sql .= "FROM appointment INNER JOIN patient ON appointment.pid=patient.pid INNER JOIN schedule ON appointment.scheduleid=schedule.scheduleid ";
$sql .= "WHERE schedule.docid={$doctorId} {$searchFilter} ";
$sql .= "GROUP BY patient.pid ORDER BY last_visit DESC";

$result = $database->query($sql);
$patients = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profdoc-patients { padding: 24px; }
        .profdoc-patients .section-card { background: #fff; border-radius: 18px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08); padding: 24px; margin-bottom: 22px; }
        .profdoc-patients .section-card h2 { font-size: 22px; margin-bottom: 18px; }
        .profdoc-patients .search-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 18px; }
        .profdoc-patients .search-row input { flex: 1; min-width: 220px; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 12px; }
        .profdoc-patients .search-row button { padding: 12px 20px; border-radius: 12px; border: none; background: #2563eb; color: #fff; font-weight: 600; cursor: pointer; }
        .profdoc-patients .table-list { width: 100%; border-collapse: collapse; }
        .profdoc-patients .table-list th, .profdoc-patients .table-list td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; }
        .profdoc-patients .table-list th { text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
        .profdoc-patients .status-pill { padding: 6px 12px; border-radius: 999px; font-size: 12px; color: #475569; background: #f8fafc; display: inline-block; }
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
    <div class="container profdoc-patients">
        <?php renderProfdocMenu('mypatients', $doctorName, $useremail); ?>
        <div class="main">
            <div class="card welcome">
                <div class="welcome-content">
                    <h1><i class="fa-solid fa-users"></i> My Patients</h1>
                    <p>Search and manage your patients, view last visits, and see upcoming appointments.</p>
                </div>
                <div class="welcome-info">
                    <i class="fa-solid fa-calendar"></i>
                    <?php echo date('F d, Y'); ?>
                </div>
            </div>
            <div class="dash-body" style="margin-top: 15px;">
                <div class="section-card">
                    <h2>My Patients</h2>
                <form method="GET" class="search-row">
                    <input type="search" name="search" placeholder="Search patient name or email" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </form>
                <table class="table-list">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Last Visit</th>
                            <th>Next Appointment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($patients)): ?>
                            <tr><td colspan="5" style="padding: 18px; text-align: center; color: #475569;">No patients found for this doctor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($patient['pname']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['pemail']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['ptel'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($patient['last_visit'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($patient['next_appointment'] ?: '-'); ?></td>
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
