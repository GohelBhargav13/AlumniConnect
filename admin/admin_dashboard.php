<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}

require '../utills/db_conn.php';
if (!isset($conn)) {
    die("Database connection not established.");
}

$total_analystics = "SELECT 
                        (SELECT COUNT(alumni_id) FROM alumni_student_master where is_registered = 1) AS alumni_count,
                        (SELECT COUNT(event_id) FROM event_master) AS total_events,
                        (SELECT COUNT(anno_id) FROM announcement_master) AS total_announcement
                        ";

$total_analystics_res = $conn->query($total_analystics);
if ($total_analystics_res) {
    $final_analystics2 =  $total_analystics_res->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="../uploads/website_images/favicon.png">
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire admin panel -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Admin Panel</h1>
            <h1 style="margin: 25px 5px;">Welcome <b><?= htmlspecialchars($_SESSION['admin_name']) ?></b> !</h1>
            <!-- Cards container -->
            <div style="display: flex; justify-content: space-around; gap: 20px;">
                <!-- Alumni Card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Alumni</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics2['alumni_count']) ?? 0 ?></p>
                </div>

                <!-- Event card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Events</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics2['total_events']) ?? 0 ?></p>
                </div>
                <!-- Announcement card -->
                <div style="flex: 1; background-color: #161b22; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #30363d;">
                    <h2 style="margin: 0; font-size: 18px;">No. of Announcement</h2>
                    <p style="font-size: 36px; margin-top: 10px;"><?= htmlspecialchars($final_analystics2['total_announcement']) ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>