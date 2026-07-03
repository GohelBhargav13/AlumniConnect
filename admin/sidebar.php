<?php
if (session_status() === PHP_SESSION_NONE) session_start();

//fetch analystics of the users

$sql_analystic = "SELECT
                        (SELECT COUNT(alumni_id) FROM alumni_student_master where is_registered = 1) AS total_alumni,
                        (SELECT COUNT(*) FROM postmaster) AS total_post
    ";

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
    <title>Document</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>

<body>
    <!-- Sidebar Navigation -->
    <div style="width: 200px; background-color: #161b22; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; border-right: 1px solid #30363d;">
        <h4 class="text-center">AlumniConnect</h4>
        <p class="text-center mb-5">Admin Dashboard</p>
        <a href="./admin_dashboard.php" onclick="sidebarTag('admin-dashboard')" id="admin-dashboard" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Dashboard</a>
        <a href="./alumni_show.php" onclick="sidebarTag('alumni-show')" id="alumni-show" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Alumni <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['total_alumni']) ?? 0 ?></sup> </a>
        <a href="./view_post_admin.php" onclick="sidebarTag('view-post')" id="view-post" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Posts <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['total_post']) ?? 0 ?></sup></a>
         <a href="./upload_student_excel.php" onclick="sidebarTag('student-show')" id="student-show" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Student record</a>
        <a href="./change_password.php" onclick="sidebarTag('change-password')" id="change-password" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Change password</a>
        <a href="./logout.php" onclick="sidebarTag('logout')" id="logout" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d; margin-top: auto;">Logout</a>
    </div>

</body>
    <script>
            function sidebarTag(sidebar_url) {
                const sidebar_id = document.getElementById(sidebar_url)
                sidebar_id.style.backgroundColor = "#21262d"
                sidebar_id.style.color = "white"
                sidebar_id.style.padding = "12px"
                sidebar_id.style.marginBottom = "10px"
                sidebar_id.style.borderRadius = "10px"
                sidebar_id.style.border = "2px solid white"
            }
    </script>
</html>