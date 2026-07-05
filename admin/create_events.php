<?php
include "../utills/db_conn.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only a logged-in admin should be able to create events.
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please login as admin.");
}

if (!isset($conn)) {
    die("Database connection not established");
}

// Only process this script when the form is actually submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["create_event_btn"])) {

    $eventName  = trim($_POST['event_name'] ?? '');
    $eventVenue = trim($_POST['venue'] ?? '');
    $eventDate  = trim($_POST['event_date'] ?? '');

    // read the additional fields for event creation
    $eventDesc = trim($_POST['description'] ?? '');
    $eventDesc = ($eventDesc === '') ? null : $eventDesc;
    $eventTime = trim($_POST['event_time'] ?? '');
    $eventTime = ($eventTime === '') ? null : $eventTime;
    $eventLink = trim($_POST['google_link'] ?? '');
    $eventLink = ($eventLink === '') ? null : $eventLink;

    // validation
    if ($eventName === '' || $eventVenue === '' || $eventDate === '') {
        header("Location: create_event.php?error=" . urlencode("Event name, venue, and date are required."));
        exit;
    }

    // preparing the insert statements
    $sql = "INSERT INTO event_master
                (event_name, event_desc, event_venue, event_date, event_time, event_additional_links, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssi",
        $eventName,
        $eventDesc,
        $eventVenue,
        $eventDate,
        $eventTime,
        $eventLink,
        $_SESSION["admin_id"]
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ./create_events.php?success=" . urlencode("Event created successfully."));
        exit();
    }

    header("Location: ./create_events.php?error=" . urlencode("Could not create event: " . mysqli_stmt_error($stmt)));
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
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

        <?php require "sidebar.php"; ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117; overflow-y: auto;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Admin Panel</h1>
            <center>
                <div style="width: 100%; max-width: 650px; margin: 0 16px;">
                    <p id="message" style="color: <?php echo isset($_GET["success"]) ? 'green' : 'red'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <div style="background-color: #1D2129; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #4B5563;">
                        <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #FFFFFF; margin-bottom: 24px;">Create Event</h1>

                        <form id="eventForm" method="post" action="">

                            <div style="margin-bottom: 24px;">
                                <label for="event_name" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Event Name</label>
                                <input type="text" id="event_name" name="event_name" required placeholder="e.g. Alumni Meet 2026" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box;">
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label for="description" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Description <span style="color:#9CA3AF; font-weight: 400;">(optional)</span></label>
                                <textarea id="description" name="description" rows="3" placeholder="Short details about the event" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box; font-family: inherit; resize: vertical;"></textarea>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label for="venue" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Venue</label>
                                <input type="text" id="venue" name="venue" required placeholder="e.g. College Auditorium" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box;">
                            </div>

                            <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                                <div style="flex: 1;">
                                    <label for="event_date" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Event Date</label>
                                    <input type="date" id="event_date" name="event_date" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box; color-scheme: dark;">
                                </div>
                                <div style="flex: 1;">
                                    <label for="event_time" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Time <span style="color:#9CA3AF; font-weight: 400;">(optional)</span></label>
                                    <input type="time" id="event_time" name="event_time" style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box; color-scheme: dark;">
                                </div>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label for="google_link" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Google Form / Meet Link <span style="color:#9CA3AF; font-weight: 400;">(optional)</span></label>
                                <input type="url" id="google_link" name="google_link" placeholder="https://forms.google.com/..." style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white; box-sizing: border-box;">
                            </div>

                            <button type="submit" name="create_event_btn" style="width: 100%; background-color: #3B82F6; color: #FFFFFF; font-weight: 700; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out;">
                                Create Event
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