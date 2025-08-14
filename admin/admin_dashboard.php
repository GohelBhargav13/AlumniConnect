<?php
include "../utills/db_conn.php";

$total_analystics = "SELECT 
                        (SELECT COUNT(*) FROM alumnimaster) AS alumni_count,
                        (SELECT COUNT(*) FROM postmaster) AS post_count,
                        (SELECT COUNT(*) FROM studentmaster WHERE req_status = 'accepted') AS student_count;";

$total_analystics_res = $conn->query($total_analystics);
if ($total_analystics_res) {
    $final_analystics =  $total_analystics_res->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin Panel</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire admin panel -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Admin Panel</h1>

            <!-- Cards container -->
            <div style="display: flex; justify-content: space-around; gap: 20px;">
                <!-- Alumni Card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Alumni</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics['alumni_count']) ?></p>
                </div>
                <!-- Student Card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Student</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics['student_count']) ?></p>
                </div>
                <!-- Post Card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Post</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics['post_count']) ?></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>