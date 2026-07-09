<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';

if (!isset($conn)) {
    die("Database connection not established.");
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $delete_query = "DELETE FROM event_master WHERE event_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        header("Location: ./manage_events.php?success=" . urlencode("Event deleted successfully."));
        exit();
    } else {
        header("Location: ./manage_events.php?error=" . urlencode("Failed to delete event."));
        exit();
    }
}

// Year filter
$selected_year = $_GET['fyear'] ?? 'all';

// Fetch distinct years for the filter dropdown
$years_query = "SELECT DISTINCT YEAR(event_date) AS yr FROM event_master ORDER BY yr DESC";
$years_res = $conn->query($years_query);
$years = [];
if ($years_res) {
    while ($row = $years_res->fetch_assoc()) {
        $years[] = $row['yr'];
    }
}

// Fetch events, filtered by year if selected, ordered descending by date
if ($selected_year !== 'all' && !empty($selected_year)) {
    $events_query = "SELECT e.*, a.admin_name FROM event_master e 
                      LEFT JOIN adminmaster a ON e.created_by = a.admin_id 
                      WHERE YEAR(e.event_date) = ? 
                      ORDER BY e.event_date DESC";
    $events_stmt = $conn->prepare($events_query);
    $events_stmt->bind_param("i", $selected_year);
    $events_stmt->execute();
    $events_res = $events_stmt->get_result();
} else {
    $events_query = "SELECT e.*, a.admin_name FROM event_master e 
                      LEFT JOIN adminmaster a ON e.created_by = a.admin_id 
                      ORDER BY e.event_date DESC";
    $events_res = $conn->query($events_query);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Manage Events</title>
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

        .status-msg.info {
            color: blue;
            background-color: rgba(15, 40, 139, 0.08);
            border: 1px solid #1F5A94;
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

        .events-list {
            max-width: 900px;
            margin: 0 auto;
        }

        .event-card {
            position: relative;
            background: #f4f8fc;
            border: 1px solid #d6e2ef;
            border-radius: 10px;
            padding: 22px 24px;
            margin-bottom: 18px;
        }

        .event-card .card-actions {
            position: absolute;
            top: 18px;
            right: 20px;
            display: flex;
            gap: 8px;
        }

        .event-card .card-actions a {
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .event-card .card-actions .edit-btn {
            background: #2E75B6;
            color: #ffffff;
        }

        .event-card .card-actions .edit-btn:hover {
            background: #1F5A94;
        }

        .event-card .card-actions .delete-btn {
            background: #ffffff;
            color: #d92d20;
            border: 1px solid #d92d20;
        }

        .event-card .card-actions .delete-btn:hover {
            background: #d92d20;
            color: #ffffff;
        }

        .event-card h3 {
            font-size: 1.1rem;
            color: #1F5A94;
            margin: 0 0 8px;
            padding-right: 160px;
        }

        .event-card p.desc {
            color: #667079;
            font-size: 0.92rem;
            margin-bottom: 12px;
        }

        .event-card .meta {
            font-size: 0.87rem;
            color: #667079;
        }

        .event-card .meta span {
            margin-right: 18px;
        }

        .event-card .posted-by {
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

            .event-card .card-actions {
                position: static;
                margin-bottom: 12px;
                justify-content: flex-end;
            }

            .event-card h3 {
                padding-right: 0;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">

        <?php include "./sidebar.php" ?>

        <div class="admin-main">
            <h1 class="admin-title">Manage Events</h1>

            <?php if (isset($_GET["success"]) || isset($_GET["error"]) || isset($_GET["info"])): ?>
                <p id="message" class="status-msg <?= isset($_GET['success']) ? 'success' : (isset($_GET["error"]) ? "error" : "info"); ?>">
                    <?= htmlspecialchars(isset($_GET['success']) ? $_GET['success'] : (isset($_GET["error"]) ?  $_GET['error'] : $_GET['info'])) ?>
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
                    <button type="submit">Filter</button>
                </form>
            </div>

            <div class="events-list">
                <?php if ($events_res && $events_res->num_rows > 0): ?>
                    <?php while ($event = $events_res->fetch_assoc()): ?>
                        <div class="event-card">
                            <div class="card-actions">
                                <a href="./create_events.php?edit=<?= htmlspecialchars($event['event_id']) ?>&current_page=<?= htmlspecialchars("edit") ?>" class="edit-btn">Edit</a>
                                <a href="./manage_events.php?delete_id=<?= htmlspecialchars($event['event_id']) ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                            </div>

                            <h3><?= htmlspecialchars($event['event_name']) ?></h3>

                            <?php if (!empty($event['event_desc'])): ?>
                                <p class="desc"><?= htmlspecialchars($event['event_desc']) ?></p>
                            <?php endif; ?>

                            <div class="meta">
                                <span>&#128197; <?= date('d M Y', strtotime($event['event_date'])) ?></span>
                                <?php if (!empty($event['event_time'])): ?>
                                    <span>&#128337; <?= date('h:i A', strtotime($event['event_time'])) ?></span>
                                <?php endif; ?>
                                <span>&#128205; <?= htmlspecialchars($event['event_venue']) ?></span>
                            </div>

                            <div class="posted-by">Posted by <?= htmlspecialchars($event['admin_name'] ?? 'Unknown') ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No events found.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>

</html>