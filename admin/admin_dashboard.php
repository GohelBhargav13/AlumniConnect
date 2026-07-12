<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}

require '../utills/db_conn.php';
include("./admin_favicon.php");
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
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #e7e7e7;
            color: #2b2f31;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            border: 1px solid #d6e2ef;
            border-radius: 10px;
            margin: 20px;
            overflow: hidden;
        }

        .admin-main {
            flex-grow: 1;
            padding: 20px;
            box-sizing: border-box;
            background-color: #e7e7e7;
        }

        .admin-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 40px;
            border-bottom: 1px solid #d6e2ef;
            padding-bottom: 20px;
            color: #2E75B6;
        }

        .admin-welcome {
            margin: 25px 5px;
            color: #2E75B6;
        }

        .cards-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 220px;
            background-color: #f4f8fc;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #d6e2ef;
        }

        .stat-card h2 {
            margin: 0;
            font-size: 18px;
            color: #1F5A94;
        }

        .stat-card p {
            font-size: 36px;
            margin-top: 10px;
            color: #2E75B6;
            font-weight: 700;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 860px) {
            .cards-container {
                flex-direction: column;
            }

            .stat-card {
                min-width: 100%;
            }
        }

        @media (max-width: 600px) {
            .admin-wrapper {
                margin: 8px;
                flex-direction: column;
            }

            .admin-title {
                font-size: 20px;
            }

            .stat-card p {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <!-- Main container for the entire admin panel -->
    <div class="admin-wrapper">

        <?php include("./sidebar.php"); ?>

        <!-- Main Content Area -->
        <div class="admin-main">
            <h1 class="admin-title">Admin Panel</h1>
            <h1 class="admin-welcome">Welcome <b><?= htmlspecialchars($_SESSION['admin_name']) ?></b> !</h1>
            <!-- Cards container -->
            <div class="cards-container">
                <!-- Alumni Card -->
                <div class="stat-card">
                    <h2>No. of Alumni</h2>
                    <p><?= htmlspecialchars($final_analystics2['alumni_count']) ?? 0 ?></p>
                </div>

                <!-- Event card -->
                <div class="stat-card">
                    <h2>No. of Events</h2>
                    <p><?= htmlspecialchars($final_analystics2['total_events']) ?? 0 ?></p>
                </div>
                <!-- Announcement card -->
                <div class="stat-card">
                    <h2>No. of Announcement</h2>
                    <p><?= htmlspecialchars($final_analystics2['total_announcement']) ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>