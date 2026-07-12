<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}

if (!isset($conn)) {
    die("Database is not connected successfully");
}

// fetch the batches of the each student
$years_query = "SELECT DISTINCT passout_year FROM alumni_student_master ORDER BY passout_year DESC";
$branches_query = "SELECT DISTINCT TRIM(branch) AS branch FROM alumni_student_master ORDER BY branch";

$years_res = $conn->query($years_query);
$years = [];
if ($years_res) {
    while ($row = $years_res->fetch_assoc()) {
        $years[] = $row['passout_year'];
    }
}

$branches_res = $conn->query($branches_query);
$branches = [];
if ($branches_res) {
    while ($row = $branches_res->fetch_assoc()) {
        $branches[] = $row['branch'];
    }
}

//fetch alumni details 
$fetch_alumni_data = "SELECT am.*, ap.* FROM alumni_student_master AS am 
                      JOIN alumni_profile AS ap ON ap.alumni_id = am.alumni_id 
                      WHERE am.is_registered = 1
                      ";
$data_res = isset($conn) ? $conn->query($fetch_alumni_data) : null;

$selected_year = $_GET["fyear"] ?? 'all';
$branch = $_GET["branch"] ?? 'all';

// full filter logic for both of the options
if ($selected_year !== 'all' && !empty($selected_year) && $branch !== 'all' && !empty($branch)) {
    $find_query = "SELECT am.*, ap.* FROM alumni_student_master AS am 
                    LEFT JOIN alumni_profile AS ap ON ap.alumni_id = am.alumni_id 
                    WHERE am.is_registered = 1 AND passout_year = ? AND TRIM(branch) = ?
                    ORDER BY am.updated_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("is", $selected_year, $branch);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif (($selected_year == 'all' || empty($selected_year)) && $branch !== 'all' && !empty($branch)) {
    $find_query = "SELECT am.*, ap.* FROM alumni_student_master AS am 
                    LEFT JOIN alumni_profile AS ap ON ap.alumni_id = am.alumni_id 
                    WHERE am.is_registered = 1 AND branch = ?
                    ORDER BY am.updated_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("s", $branch);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif ($selected_year !== 'all' && !empty($selected_year) && ($branch == 'all' || empty($branch))) {
    $find_query = "SELECT am.*, ap.* FROM alumni_student_master AS am 
                    LEFT JOIN alumni_profile AS ap ON ap.alumni_id = am.alumni_id 
                    WHERE am.is_registered = 1 AND passout_year = ?
                    ORDER BY am.updated_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("i", $selected_year);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} else {
    $find_query = "SELECT am.*, ap.* FROM alumni_student_master AS am 
                    LEFT JOIN alumni_profile AS ap ON ap.alumni_id = am.alumni_id 
                    WHERE am.is_registered = 1
                    ORDER BY am.updated_at DESC
                    ";
    $data_res = $conn->query($find_query);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            margin: 5px;
            padding: 0;
            font-family: "Poppins", sans-serif;
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

        .alumni-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
        }

        .alumni-card {
            width: calc(50% - 10px);
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #d6e2ef;
            box-sizing: border-box;
        }

        .alumni-card p {
            margin: 5px;
            font-size: 16px;
            color: #2b2f31;
        }

        .alumni-card h2 {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 5px;
            color: #1F5A94;
        }

        .alumni-card a {
            text-decoration: none;
            color: #2E75B6;
        }

        .avatar {
            width: 80px;
            height: 80px;
            background: #2E75B6;
            color: #ffffff;
            font-size: 30px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            margin-bottom: 12px;
        }

        .filter-bar {
            max-width: 900px;
            margin: 0 auto 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-bar label {
            font-size: 0.9rem;
            color: #667079;
        }

        .filter-bar select {
            padding: 9px 14px;
            border: 1px solid #d6e2ef;
            border-radius: 6px;
            background: #ffffff;
            color: #2b2f31;
            font-size: 0.9rem;
            font-family: inherit;
        }

        .filter-bar button[type="submit"] {
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            background: #2E75B6;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .filter-bar button[type="submit"]:hover {
            background: #1F5A94;
        }

        .empty-state {
            width: 100%;
            text-align: center;
            color: #667079;
            padding: 60px 0;
            font-size: 0.95rem;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 860px) {
            .alumni-card {
                width: 100%;
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
        }
    </style>
</head>

<body>

    <!-- Main container for the entire page -->
    <div class="admin-wrapper">

        <!-- Sidebar Navigation -->
        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div class="admin-main">
            <h1 class="admin-title">Alumni's</h1>
            <div class="filter-bar">
                <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                    <label for="fyear">Filter by Year</label>
                    <select id="fyear" name="fyear">
                        <option value="all" <?= $selected_year === 'all' ? 'selected' : '' ?>>-- Show All --</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year) ?>" <?= ((string) $selected_year === (string) $year) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="branch">Filter by Branch</label>
                    <select id="branch" name="branch">
                        <option value="all" <?= $branch === 'all' ? 'selected' : '' ?>>-- Show All --</option>
                        <?php foreach ($branches as $br): ?>
                            <option value="<?= htmlspecialchars($br) ?>" <?= ((string) $branch === (string) $br) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($br) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Filter</button>
                </form>
            </div>

            <!-- Alumni cards container -->
            <div class="alumni-cards">
                <!-- Alumni Card 1 -->
                <?php
                if (isset($data_res) && $data_res->num_rows > 0):
                    while ($row = $data_res->fetch_assoc()):
                ?>
                        <div class="alumni-card">
                            <div class="avatar"><?= strtoupper(substr($row['alumni_name'] ?? 'A', 0, 1)) ?></div>
                            <h2><?= htmlspecialchars($row['alumni_name'] ?? "NULL") ?></h2>
                            <p><b>Passout Year </b>: <?= htmlspecialchars($row['passout_year'] ?? "NULL") ?></p>
                            <p><b>Company </b>: <?= htmlspecialchars($row['alumni_company'] ?? "NULL") ?></p>
                            <p><b>Branch </b>: <?= htmlspecialchars($row['branch'] ?? "NULL") ?></p>
                            <p><b>College </b>: <?= htmlspecialchars($row['alumni_college'] ?? "GEC MODASA") ?></p>
                            <p><b>LinkedIn </b>: 
                            <?php if (!empty($row['alumni_linkedin_link'])): ?>
                                <a href="<?= htmlspecialchars($row['alumni_linkedin_link']) ?>" target="_blank">Profile</a>
                            <?php else: ?>
                                NULL
                            <?php endif; ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No Alumni found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>