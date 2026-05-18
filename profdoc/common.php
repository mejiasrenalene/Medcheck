<?php
session_start();
include("../connection.php");

function requireDoctor() {
    global $database;
    if (!isset($_SESSION['user']) || $_SESSION['usertype'] !== 'd') {
        header('Location: ../login.php');
        exit;
    }

    $useremail = $database->real_escape_string($_SESSION['user']);
    $doctorQuery = $database->query("SELECT * FROM doctor WHERE docemail='$useremail' LIMIT 1");
    if (!$doctorQuery || $doctorQuery->num_rows === 0) {
        header('Location: ../login.php');
        exit;
    }

    return $doctorQuery->fetch_assoc();
}

function getDoctorSpecialization($database, $specialtyId) {
    if (empty($specialtyId) || !is_numeric($specialtyId)) {
        return 'General Practice';
    }

    $specialtyId = intval($specialtyId);
    $result = $database->query("SELECT sname FROM specialties WHERE id={$specialtyId} LIMIT 1");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['sname'];
    }

    return 'General Practice';
}

function renderProfdocMenu($activePage, $doctorName, $useremail) {
    $items = [
        ['key' => 'dashboard', 'href' => 'dashboard.php', 'label' => 'Dashboard', 'icon' => 'dashbord'],
        ['key' => 'appointments', 'href' => 'UpcomingAppointments.php', 'label' => 'Upcoming Appointments', 'icon' => 'appoinment'],
        ['key' => 'quickstats', 'href' => 'QuickStats.php', 'label' => 'Quick Stats', 'icon' => 'session'],
        ['key' => 'notifications', 'href' => 'notifications.php', 'label' => 'Notifications', 'icon' => 'patient'],
        ['key' => 'actions', 'href' => 'actions.php', 'label' => 'Actions', 'icon' => 'settings'],
        ['key' => 'mypatients', 'href' => 'mypatients.php', 'label' => 'My Patients', 'icon' => 'patient'],
    ];

    echo '<div class="menu"><table class="menu-container" border="0">';
    echo '<tr><td style="padding:10px" colspan="2"><table border="0" class="profile-container"><tr><td width="30%" style="padding-left:20px"><img src="../img/user.png" alt="Doctor avatar" width="100%" style="border-radius:50%;"></td><td style="padding:0;margin:0;"><p class="profile-title">' . htmlspecialchars(substr($doctorName, 0, 16)) . '</p><p class="profile-subtitle">' . htmlspecialchars($useremail) . '</p></td></tr><tr><td colspan="2"><a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a></td></tr></table></td></tr>';

    foreach ($items as $item) {
        $active = $activePage === $item['key'];
        $classes = 'menu-btn menu-icon-' . $item['icon'];
        $linkClasses = 'non-style-link-menu' . ($active ? ' active' : '');
        echo '<tr class="menu-row"><td class="' . $classes . '"><a href="' . $item['href'] . '" class="' . $linkClasses . '"><div><p class="menu-text">' . $item['label'] . '</p></div></a></td></tr>';
    }

    echo '</table></div>';
}

function statusLabel($date, $today) {
    if ($date === $today) {
        return 'Confirmed';
    }
    if ($date > $today) {
        return 'Pending';
    }
    return 'Completed';
}

function countRows($database, $sql) {
    $result = $database->query($sql);
    return $result ? $result->num_rows : 0;
}
