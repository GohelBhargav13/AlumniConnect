<?php
require_once '../utills/db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Enroll_no'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['student_id'])) {
    $_SESSION['message'] = ["success" => false, "final_msg" => "Id is not set"];
    header("Location: ./student_dashboard.php");
    exit();
}

$student_id = $_SESSION['student_id'];

//optional sql query that work same as original used....

// $sql = "SELECT ap.*, p.post_title,p.post_desc
//  FROM applystudentmaster ap 
//  JOIN postmaster p ON p.post_id = ap.post_id WHERE student_id = ?";

//Fetch the all details of the post that is applied by the student...
$sql = "SELECT P.*, ap.post_id,ap.student_id FROM postmaster as p JOIN applystudentmaster ap ON ap.post_id = p.post_id WHERE student_id = ?";
$sql_stmt = $conn->prepare($sql);
$sql_stmt->bind_param('i', $student_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student applied post | Alumni Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px 40px;
            width: calc(100% - 250px);
        }

        .post-card {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            transition: 0.3s ease-in-out;
        }

        .post-card:hover {
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        }

        .post-title {
            font-size: 20px;
            font-weight: 600;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .post-desc {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .post-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #eaeaea;
            padding-top: 12px;
            margin-top: 10px;
        }

        .no-post {
            text-align: center;
            font-size: 16px;
            color: #888;
        }
    </style>
</head>

<body>
    <!-- Sidebar added... -->
    <?php include './sidebar.php' ?>
    <div class="main-content">
        <!-- <a href="./alumni_dashboard.php"><button class="apply-btn" style="margin-bottom: 20px;">Back</button></a> -->
        <h2 style="text-align: center; padding:10px; margin-bottom: 15px; font-size: larger;" class="badge badge-secondary">Applied Jobs</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <p id="message"><?= htmlspecialchars($_SESSION['message']['final_msg']) ?></p>
            <script>
                const message = document.getElementById('message');
                setTimeout(() => {
                    message.innerText = '';
                }, 2000);
            </script>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php
        $sql_stmt->execute();
        $result = $sql_stmt->get_result();
        if ($result->num_rows > 0):

            while ($row = $result->fetch_assoc()):
        ?>
                <div class="post-card">
                    <input type="hidden" name="post_name" value="<?= htmlspecialchars($row['post_title']) ?>">
                    <h2 class="post-title">🚀 <?= htmlspecialchars($row['post_title']) ?></h2>
                    <p class="post-desc"><?= htmlspecialchars($row['post_desc']) ?></p>

                    <div class="post-info-grid">
                        <div><strong>📍 Location:</strong> <?= htmlspecialchars($row['post_location']) ?></div>
                        <div><strong>🛠 Skills:</strong> <?= htmlspecialchars($row['post_req_skill']) ?></div>
                        <div><strong>🗺 Roadmap:</strong> <?= htmlspecialchars($row['post_ded_roadmap']) ?></div>
                        <div><strong>📅 Type:</strong> <?= htmlspecialchars($row['post_job_type']) ?></div>
                    </div>

                    <div class="card-footer">
                        <span>Posted on: <?= htmlspecialchars(date('d-m-Y, l', strtotime($row['post_created_at']))) ?></span>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="no-post">No Applied Jobs</div>
        <?php endif; ?>
    </div>

</body>

</html>