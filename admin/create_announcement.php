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
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire admin panel -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <!-- add the sidebar -->

        <?php require "sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117; overflow-y: auto;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Admin Panel</h1>
            <center>
                <div style="width: 100%; max-width: 650px; margin: 0 16px;">
                    <p id="message" style="color: <?php echo isset($_GET["success"]) ? 'green' : 'red'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <div style="background-color: #1D2129; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #4B5563;">
                        <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #FFFFFF; margin-bottom: 24px;">Create Announcement</h1>

                        <form id="announcementForm" method="post" action="">

                            <div style="margin-bottom: 24px;">
                                <label for="announcement_title" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Announcement Title</label>
                                <input type="text" id="announcement_title" name="announcement_title" required placeholder="e.g. Semester Exam Schedule Released" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box;">
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label for="description" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Description</label>
                                <textarea id="description" name="description" rows="4" required placeholder="Full details of the announcement" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box; font-family: inherit; resize: vertical;"></textarea>
                            </div>

                            <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                                <div style="flex: 1;">
                                    <label for="priority" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Priority</label>
                                    <select id="priority" name="priority" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; color: white; box-sizing: border-box;">
                                        <option value="normal">Normal</option>
                                        <option value="important">Important</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                 <div style="margin-bottom: 24px;">
                                    <label for="expiry_date" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Show Until <span style="color:#9CA3AF; font-weight: 400;">(optional)</span></label>
                                    <input type="date" id="expiry_date" name="expiry_date" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box; color-scheme: dark;">
                            </div>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label for="attachment_link" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Attachment / Reference Link <span style="color:#9CA3AF; font-weight: 400;">(optional)</span></label>
                                <input type="url" id="attachment_link" name="attachment_link" placeholder="Link to notice PDF, circular, etc." style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box;">
                            </div>

                            <button type="submit" name="create_announcement_btn" style="width: 100%; background-color: #3B82F6; color: #FFFFFF; font-weight: 700; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out;">
                                Post Announcement
                            </button>
                        </form>

                        <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #F87171; display: none;"></div>
                    </div>
                </div>
            </center>
        </div>
    </div>

</body>

</html>