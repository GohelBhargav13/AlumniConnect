<?php
include '../utills/db_conn.php';
//fetch alumni details 
$fetch_student_data = "SELECT * FROM studentmaster";
$data_res = $conn->query($fetch_student_data);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin</title>
    <style>
        .avatar {
            width: 80px;
            height: 80px;
            background: #0d1117;
            color: white;
            font-size: 30px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            margin-bottom: 12px;
        }
    </style>
</head>

<body style="margin: 5px; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire page -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <!-- Sidebar Navigation -->
        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Student's</h1>

            <!-- Alumni cards container -->
            <div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 20px;">
                <?php
                if ($data_res):
                    while ($row = $data_res->fetch_assoc()):

                ?>
                        <!-- Alumni Card 1 -->
                        <div style="width: calc(50% - 10px); background-color: #161b22; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #30363d; box-sizing: border-box;">
                            <div class="avatar"><?= strtoupper(substr($row['student_name'] ?? 'A', 0, 1)) ?></div>
                            <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 5px;"><?= htmlspecialchars($row['student_name']) ?></h2>
                            <p style="margin: 5px; font-size: 16px;"><b>Passout Year </b>: <?= htmlspecialchars($row['student_pass_year']) ?></p>
                            <p style="margin: 5px; font-size: 16px;"><b>College </b>: <?= htmlspecialchars($row['student_college']) ?></p>
                            <p style="margin: 5px; font-size: 16px;"><b>Department </b>: <?= htmlspecialchars($row['student_department']) ?></p>
                            <p style="margin: 5px; font-size: 16px;"><b>LinkedIn </b>: <a href="<?= htmlspecialchars($row['student_linkedIn']) ?>" style="text-decoration: none; color: white;"> <?= htmlspecialchars($row['student_linkedIn']) ?></a></p>
                        </div>
                <?php endwhile;
                endif; ?>
            </div>
        </div>
    </div>

</body>

</html>