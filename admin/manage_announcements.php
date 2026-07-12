<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';
include("./admin_favicon.php");

if (!isset($conn)) {
    die("Database connection not established.");
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $delete_query = "DELETE FROM announcement_master WHERE anno_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        header("Location: ./manage_announcements.php?success=" . urlencode("Announcement deleted successfully."));
        exit();
    } else {
        header("Location: ./manage_announcements.php?error=" . urlencode("Failed to delete announcement."));
        exit();
    }
}

// Year filter (based on created_at / posted date)
$selected_year = $_GET['fyear'] ?? 'all';
$allowed_priority = ["Normal", "Important", "Urgent"];
$announcement_priority = $_GET["priority"] ?? 'all';

// Fetch distinct years for the filter dropdown
$years_query = "SELECT DISTINCT YEAR(created_at) AS yr FROM announcement_master ORDER BY yr DESC";
$years_res = $conn->query($years_query);
$years = [];
if ($years_res) {
    while ($row = $years_res->fetch_assoc()) {
        $years[] = $row['yr'];
    }
}

// full filter logic for both of the options
if ($selected_year !== 'all' && !empty($selected_year) && $announcement_priority !== 'all' && !empty($announcement_priority)) {
    $anno_query = "SELECT a.*, ad.admin_name FROM announcement_master a
                    LEFT JOIN adminmaster ad ON a.created_by = ad.admin_id
                    WHERE YEAR(a.created_at) = ? AND a.anno_type = ?
                    ORDER BY a.created_at DESC";
    $anno_stmt = $conn->prepare($anno_query);
    $anno_stmt->bind_param("is", $selected_year, $announcement_priority);
    $anno_stmt->execute();
    $anno_res = $anno_stmt->get_result();
} elseif (($selected_year == 'all' || empty($selected_year)) && $announcement_priority !== 'all' && !empty($announcement_priority)) {
    $anno_query = "SELECT a.*, ad.admin_name FROM announcement_master a
                    LEFT JOIN adminmaster ad ON a.created_by = ad.admin_id
                    WHERE a.anno_type = ?
                    ORDER BY a.created_at DESC";
    $anno_stmt = $conn->prepare($anno_query);
    $anno_stmt->bind_param("s", $announcement_priority);
    $anno_stmt->execute();
    $anno_res = $anno_stmt->get_result();
} elseif ($selected_year !== 'all' && !empty($selected_year) && ($announcement_priority == 'all' || empty($announcement_priority))) {
    $anno_query = "SELECT a.*, ad.admin_name FROM announcement_master a
                    LEFT JOIN adminmaster ad ON a.created_by = ad.admin_id
                    WHERE YEAR(a.created_at) = ?
                    ORDER BY a.created_at DESC";
    $anno_stmt = $conn->prepare($anno_query);
    $anno_stmt->bind_param("i", $selected_year);
    $anno_stmt->execute();
    $anno_res = $anno_stmt->get_result();
} else {
    $anno_query = "SELECT a.*, ad.admin_name FROM announcement_master a
                    LEFT JOIN adminmaster ad ON a.created_by = ad.admin_id
                    ORDER BY a.created_at DESC";
    $anno_res = $conn->query($anno_query);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Manage Announcements</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
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
            background-color: #ffffff;
            overflow-y: auto;
        }

        .admin-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            border-bottom: 1px solid #d6e2ef;
            padding-bottom: 20px;
            color: #2E75B6;
        }

        .status-msg {
            max-width: 700px;
            margin: 0 auto 20px;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        .status-msg.success {
            color: #0a7d3e;
            background: rgba(10, 125, 62, 0.08);
            border: 1px solid #0a7d3e;
        }

        .status-msg.error {
            color: #d92d20;
            background: rgba(217, 45, 32, 0.08);
            border: 1px solid #d92d20;
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

        .anno-list {
            max-width: 900px;
            margin: 0 auto;
        }

        .anno-card {
            position: relative;
            background: #f4f8fc;
            border: 1px solid #d6e2ef;
            border-radius: 10px;
            padding: 22px 24px;
            margin-bottom: 18px;
        }

        .anno-card .card-actions {
            position: absolute;
            top: 18px;
            right: 20px;
            display: flex;
            gap: 8px;
        }

        .anno-card .card-actions a {
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .anno-card .card-actions .edit-btn {
            background: #2E75B6;
            color: #ffffff;
        }

        .anno-card .card-actions .edit-btn:hover {
            background: #1F5A94;
        }

        .anno-card .card-actions .delete-btn {
            background: #ffffff;
            color: #d92d20;
            border: 1px solid #d92d20;
        }

        .anno-card .card-actions .delete-btn:hover {
            background: #d92d20;
            color: #ffffff;
        }

        .badge-light {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .badge-light.normal {
            background: #eceff0;
            color: #667079;
        }

        .badge-light.important {
            background: #fbe9b4;
            color: #7a5d00;
        }

        .badge-light.urgent {
            background: #f6d5cf;
            color: #a3372a;
        }

        .anno-card h3 {
            font-size: 1.1rem;
            color: #1F5A94;
            margin: 0 0 8px;
            padding-right: 160px;
        }

        .anno-card p.desc {
            color: #667079;
            font-size: 0.92rem;
            margin-bottom: 12px;
        }

        .anno-card .meta {
            font-size: 0.87rem;
            color: #667079;
        }

        .anno-card .meta span {
            margin-right: 18px;
        }

        .anno-card .posted-by {
            font-size: 0.8rem;
            color: #667079;
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            color: #667079;
            padding: 60px 0;
            font-size: 0.95rem;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 700px) {
            .admin-wrapper {
                margin: 8px;
                flex-direction: column;
            }

            .filter-bar {
                justify-content: flex-start;
                width: 100%;
            }

            .filter-bar select {
                flex: 1;
            }

            .anno-card .card-actions {
                position: static;
                margin-bottom: 12px;
                justify-content: flex-end;
            }

            .anno-card h3 {
                padding-right: 0;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">

        <?php include "./sidebar.php" ?>

        <div class="admin-main">
            <h1 class="admin-title">Manage Announcements</h1>

            <?php if (isset($_GET["success"]) || isset($_GET["error"])): ?>
                <p id="message" class="status-msg <?= isset($_GET['success']) ? 'success' : 'error'; ?>">
                    <?= htmlspecialchars($_GET['success'] ?? $_GET['error']); ?>
                </p>
            <?php endif; ?>
            <script>
                const message = document.getElementById("message");
                setTimeout(() => {
                    message.style.display = "none";
                }, 2000);
            </script>

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
                    <label for="fyear">Filter by Priority</label>
                    <select id="priority" name="priority">
                        <option value="all" <?= $selected_year === 'all' ? 'selected' : '' ?>>-- Show All --</option>
                        <?php foreach ($allowed_priority as $pri): ?>
                            <option value="<?= htmlspecialchars($pri) ?>" <?= ((string) $announcement_priority === (string) $pri) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pri) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Filter</button>
                </form>
            </div>

            <div class="anno-list">
                <?php if ($anno_res && $anno_res->num_rows > 0): ?>
                    <?php while ($anno = $anno_res->fetch_assoc()): ?>
                        <div class="anno-card">
                            <div class="card-actions">
                                <a href="./create_announcement.php?edit=<?= htmlspecialchars($anno['anno_id']) ?>&current_page=<?= htmlspecialchars("edit") ?>" class="edit-btn">Edit</a>
                                <a href="./manage_announcements.php?delete_id=<?= htmlspecialchars($anno['anno_id']) ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                            </div>

                            <span class="badge-light <?= htmlspecialchars(strtolower($anno['anno_type'])) ?>"><?= htmlspecialchars($anno['anno_type']) ?></span>
                            <h3><?= htmlspecialchars($anno['anno_title']) ?></h3>

                            <?php if (!empty($anno['anno_desc'])): ?>
                                <p class="desc"><?= htmlspecialchars($anno['anno_desc']) ?></p>
                            <?php endif; ?>

                            <div class="meta">
                                <span>&#128197; Posted <?= date('d M Y', strtotime($anno['created_at'])) ?></span>
                                <?php if (!empty($anno['anno_show_until'])): ?>
                                    <span>&#8987; Show Until <?= date('d M Y', strtotime($anno['anno_show_until'])) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="posted-by">Posted by <?= htmlspecialchars($anno['admin_name'] ?? 'Unknown') ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No announcements found.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>

</html>