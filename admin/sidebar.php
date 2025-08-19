<?php
if (session_status() === PHP_SESSION_NONE) session_start();

//fetch analystics of the users

$sql_analystic = "SELECT
                        (SELECT COUNT(*) FROM studentmaster WHERE req_status = 'pending') AS pending_req_student,
                        (SELECT COUNT(*) FROM alumnimaster WHERE req_status = 'pending') AS pending_req_alumni,
                        (SELECT COUNT(*) FROM  studentmaster) AS total_student,
                        (SELECT COUNT(*) FROM alumnimaster) AS total_alumni,
                        (SELECT COUNT(*) FROM postmaster) AS total_post
    ";

$total_analystics_res = $conn->query($sql_analystic);
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
        <a href="./admin_dashboard.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Dashboard</a>
        <a href="./alumni_show.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Alumni <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['total_alumni']) ?? 0 ?></sup> </a>
        <a href="./student_show.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Student <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['total_student']) ?? 0 ?></sup></a>
        <a href="./view_post_admin.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Posts <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['total_post']) ?? 0 ?></sup></a>
        <a href="./student_new_req.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">New Request Student <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['pending_req_student']) ?? 0 ?></sup></a>
        <a href="./alumni_new_req.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">New Request Alumni <sup style="background-color:white; color:black; padding:1px 3px; border-radius:12px; font-weight: bolder;"><?= htmlspecialchars($final_analystics['pending_req_alumni']) ?? 0 ?></sup></a>
        <a href="./change_password.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d;">Change password</a>
        <a href="./logout.php" style="background-color: #21262d; color: white; padding: 12px; margin-bottom: 10px; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #30363d; margin-top: auto;">Logout</a>
    </div>

</body>

</html>