<?php
if (session_status() === PHP_SESSION_NONE) session_start();

//fetch analystics of the users
$sql_analystic = "SELECT COUNT(alumni_id) AS total_alumni FROM alumni_student_master 
                  where is_registered = 1";

$total_analystics_res = isset($conn) ? $conn->query($sql_analystic) : null;
if ($total_analystics_res) {
    $final_analystics =  $total_analystics_res->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Portal | GEC Modasa | Admin App</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link rel="icon" type="image/png" href="../assets/gec_favicon.png">
    <style>
        .admin-sidebar {
            width: 200px;
            background-color: #f4f8fc;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #d6e2ef;
        }

        .admin-sidebar h4,
        .admin-sidebar p {
            color: #2E75B6;
        }

        .sidebar-link {
            background-color: #2E75B6;
            color: #ffffff;
            padding: 12px;
            margin-bottom: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid #1F5A94;
        }

        .sidebar-link:hover {
            background-color: #1F5A94;
            color: #ffffff;
        }

        .sidebar-link.logout {
            margin-top: auto;
        }

        .sidebar-badge {
            background-color: #ffffff;
            color: #2E75B6;
            padding: 1px 3px;
            border-radius: 12px;
            font-weight: bolder;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 860px) {
            .admin-sidebar {
                width: 160px;
            }
        }

        @media (max-width: 600px) {
            .admin-sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
                border-right: none;
                border-bottom: 1px solid #d6e2ef;
            }

            .admin-sidebar h4,
            .admin-sidebar p {
                width: 100%;
            }

            .sidebar-link {
                flex: 1 1 auto;
                margin-bottom: 0;
            }

            .sidebar-link.logout {
                margin-top: 0;
            }
        }
    </style>

</head>

<body>
    <!-- Sidebar Navigation -->
    <div class="admin-sidebar">
        <h4 class="text-center">AlumniConnect</h4>
        <p class="text-center mb-5">Admin Dashboard</p>
        <a href="./admin_dashboard.php" onclick="sidebarTag('admin-dashboard')" id="admin-dashboard" class="sidebar-link">Dashboard</a>
        <a href="./manage_events.php" onclick="sidebarTag('show-events')" id="show-events" class="sidebar-link">Total Events</a>
        <a href="./manage_announcements.php" onclick="sidebarTag('show-events')" id="show-events" class="sidebar-link">Total Announcements</a>
        <a href="./alumni_show.php" onclick="sidebarTag('alumni-show')" id="alumni-show" class="sidebar-link">Alumni <sup class="sidebar-badge"><?= htmlspecialchars($final_analystics['total_alumni']) ?? 0 ?></sup> </a>
        <a href="./upload_student_excel.php" onclick="sidebarTag('student-show')" id="student-show" class="sidebar-link">Student record</a>
        <a href="./create_events.php" onclick="sidebarTag('events')" id="events" class="sidebar-link">Events</a>
        <a href="./create_announcement.php" onclick="sidebarTag('announcements')" id="announcements" class="sidebar-link">Annoucements</a>
        <a href="./change_password.php" onclick="sidebarTag('change-password')" id="change-password" class="sidebar-link">Change password</a>
        <a href="./logout.php" onclick="sidebarTag('logout')" id="logout" class="sidebar-link logout">Logout</a>
    </div>

</body>
<script>
    function sidebarTag(sidebar_url) {
        const sidebar_id = document.getElementById(sidebar_url)
        sidebar_id.style.backgroundColor = "#1F5A94"
        sidebar_id.style.color = "white"
        sidebar_id.style.padding = "12px"
        sidebar_id.style.marginBottom = "10px"
        sidebar_id.style.borderRadius = "10px"
        sidebar_id.style.border = "2px solid #2E75B6"
    }
</script>

</html>