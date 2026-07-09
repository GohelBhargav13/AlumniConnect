<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../utills/db_conn.php'; // gives us $conn

if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please login as admin.");
}

if (!isset($conn)) {
    die("Database connection is not established");
}

$update_announcement_details = null;
if (isset($_GET["current_page"]) && isset($_GET["edit"])) {
    $anno_id = $_GET["edit"] ?? 0;
    $current_page = $_GET["current_page"] ?? "";

    // fetch the update event details
    $fetch_query = "SELECT * FROM announcement_master WHERE anno_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);
    $fetch_stmt->bind_param("i", $anno_id);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ./manage_announcements.php?error=" . urlencode("Announcement not found."));
        exit();
    }

    $update_announcement_details = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Required fields ----
    $annoTitle = trim($_POST['announcement_title'] ?? '');
    $annoDesc  = trim($_POST['description'] ?? '');
    $annoType  = trim($_POST['priority'] ?? 'normal');

    // ---- Optional fields 
    $showUntil = trim($_POST['expiry_date'] ?? '');
    $showUntil = ($showUntil === '') ? null : $showUntil;

    $annoLink = trim($_POST['attachment_link'] ?? '');
    $annoLink = ($annoLink === '') ? null : $annoLink;
    $createdBy = $_SESSION['admin_id'];

    // ---- Validate required fields
    if ($annoTitle === '' || $annoDesc === '') {
        header("Location: create_announcement.php?error=" . urlencode("Title and description are required."));
        exit;
    }

    if (isset($anno_id) && isset($current_page) && $current_page == "edit") {
        $update_query = "UPDATE announcement_master 
                          SET anno_title = ?, anno_desc = ?, anno_type = ?, anno_show_until = ?,  anno_additional_links = ? 
                          WHERE anno_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssssi", $annoTitle, $annoDesc, $annoType, $showUntil, $annoLink, $anno_id);

        if ($update_stmt->execute()) {
            header("Location: ./manage_announcements.php?success=" . urlencode("Announcement updated successfully."));
            exit();
        } else {
            header("Location: ./create_announcement.php?edit=" . urlencode($anno_id) . "&error=" . urlencode("Failed to update announcement."));
            exit();
        }
    } else {

        // ---- Prepared statement
        $sql = "INSERT INTO announcement_master
                (anno_title, anno_desc, anno_type, anno_show_until, anno_additional_links, created_by)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $annoTitle,
            $annoDesc,
            $annoType,
            $showUntil,
            $annoLink,
            $createdBy
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: create_announcement.php?success=" . urlencode("Announcement posted successfully."));
        } else {
            header("Location: create_announcement.php?error=" . urlencode("Could not post announcement: " . mysqli_stmt_error($stmt)));
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
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
            margin-bottom: 40px;
            border-bottom: 1px solid #d6e2ef;
            padding-bottom: 20px;
            color: #2E75B6;
        }

        .form-wrapper {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
        }

        .form-card {
            background-color: #f4f8fc;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #d6e2ef;
            box-sizing: border-box;
        }

        .form-card h1 {
            font-size: 30px;
            font-weight: 600;
            text-align: center;
            color: #2E75B6;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #667079;
            margin-bottom: 8px;
        }

        .form-group label .optional {
            color: #9CA3AF;
            font-weight: 400;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            background-color: #ffffff;
            border: 1px solid #d6e2ef;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease-in-out;
            color: #2b2f31;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
        }

        .priority-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .priority-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .submit-btn {
            width: 100%;
            background-color: #2E75B6;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .submit-btn:hover {
            background-color: #1F5A94;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 600px) {
            .admin-wrapper {
                margin: 8px;
                flex-direction: column;
            }

            .admin-title {
                font-size: 20px;
            }

            .form-card {
                padding: 22px;
            }

            .priority-row {
                flex-direction: column;
                gap: 24px;
            }
        }
    </style>
</head>

<body>

    <!-- Main container for the entire admin panel -->
    <div class="admin-wrapper">

        <!-- add the sidebar -->

        <?php require "sidebar.php" ?>

        <!-- Main Content Area -->
        <div class="admin-main">
            <h1 class="admin-title">Admin Panel</h1>
            <center>
                <div class="form-wrapper">
                    <p id="message" style="color: <?php echo isset($_GET["success"]) ? '#0a7d3e' : '#d92d20'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <?php if (isset($current_page) && $current_page == "edit"): ?>
                        <div class="form-card">
                            <h1>Create Announcement</h1>

                            <form id="announcementForm" method="post" action="">

                                <div class="form-group">
                                    <label for="announcement_title">Announcement Title</label>
                                    <input type="text" id="announcement_title" name="announcement_title" value="<?= htmlspecialchars($update_announcement_details["anno_title"]) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($update_announcement_details["anno_desc"]) ?></textarea>
                                </div>

                                <div class="priority-row">
                                    <div class="form-group">
                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority">
                                            <option value="normal" <?= htmlspecialchars($update_announcement_details["anno_type"] == "Normal") ? 'selected' : '' ?>>Normal</option>
                                            <option value="important" <?= htmlspecialchars($update_announcement_details["anno_type"] == "Important") ? 'selected' : '' ?>>Important</option>
                                            <option value="urgent" <?= htmlspecialchars($update_announcement_details["anno_type"] == "Urgent") ? 'selected' : '' ?>>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="expiry_date">Show Until <span class="optional">(optional)</span></label>
                                        <input type="date" id="expiry_date"  value="<?= 
                                        htmlspecialchars($update_announcement_details["anno_show_until"]) ?>" name="expiry_date">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="attachment_link">Attachment / Reference Link <span class="optional">(optional)</span></label>
                                    <input type="url" id="attachment_link" name="attachment_link" placeholder="Link to notice PDF, circular, etc."
                                    value="<?= htmlspecialchars($update_announcement_details["anno_additional_links"]) ?>"
                                    
                                    >
                                </div>

                                <button type="submit" name="create_announcement_btn" class="submit-btn">
                                    <?= isset($anno_id) ? "Update Event" : ""   ?>
                                </button>
                            </form>

                            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
                        </div>
                    <?php else: ?>
                        <!-- create announcement form code -->
                        <div class="form-card">
                            <h1>Create Announcement</h1>

                            <form id="announcementForm" method="post" action="">

                                <div class="form-group">
                                    <label for="announcement_title">Announcement Title</label>
                                    <input type="text" id="announcement_title" name="announcement_title" required placeholder="e.g. Semester Exam Schedule Released">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" rows="4" required placeholder="Full details of the announcement"></textarea>
                                </div>

                                <div class="priority-row">
                                    <div class="form-group">
                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority">
                                            <option value="normal">Normal</option>
                                            <option value="important">Important</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="expiry_date">Show Until <span class="optional">(optional)</span></label>
                                        <input type="date" id="expiry_date" name="expiry_date">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="attachment_link">Attachment / Reference Link <span class="optional">(optional)</span></label>
                                    <input type="url" id="attachment_link" name="attachment_link" placeholder="Link to notice PDF, circular, etc.">
                                </div>

                                <button type="submit" name="create_announcement_btn" class="submit-btn">
                                    Post Announcement
                                </button>
                            </form>

                            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </center>
        </div>
    </div>

</body>

</html>