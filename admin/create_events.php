<?php
include "../utills/db_conn.php";
include("./admin_favicon.php");

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

// get the current page is edit page or the update page
$update_event_details = null;
if (isset($_GET["current_page"]) && isset($_GET["edit"])) {
    $event_id = $_GET["edit"] ?? 0;
    $current_page = $_GET["current_page"] ?? "";

    // fetch the update event details
    $fetch_query = "SELECT * FROM event_master WHERE event_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);
    $fetch_stmt->bind_param("i", $event_id);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ./manage_events.php?error=" . urlencode("Event not found."));
        exit();
    }

    $update_event_details = $result->fetch_assoc();
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

    if (isset($event_id) && isset($current_page) && $current_page == "edit") {
        $update_query = "UPDATE event_master 
                          SET event_name = ?, event_desc = ?, event_venue = ?, event_date = ?, event_time = ?, event_additional_links = ? 
                          WHERE event_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssssi", $eventName, $eventDesc, $eventVenue, $eventDate, $eventTime, $eventLink, $event_id);

        if ($update_stmt->execute()) {
            header("Location: ./manage_events.php?success=" . urlencode("Event updated successfully."));
            exit();
        } else {
            header("Location: ./create_events.php?edit=" . urlencode($event_id) . "&error=" . urlencode("Failed to update event."));
            exit();
        }
    } else {
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
        .form-group textarea {
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

        .date-time-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .date-time-row .form-group {
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

            .date-time-row {
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

        <?php require "sidebar.php"; ?>

        <!-- Main Content Area -->
        <div class="admin-main">
            <h1 class="admin-title">Admin Panel</h1>
            <center>
                <div class="form-wrapper">
                    <p id="message" style="color: <?php echo isset($_GET["success"]) ? '#0a7d3e' : '#d92d20'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <!-- event edit page -->
                    <?php if (isset($current_page) && $current_page == "edit"): ?>
                        <div class="form-card">
                            <h1>Update Event</h1>

                            <form id="eventForm" method="post" action="">

                                <div class="form-group">
                                    <label for="event_name">Event Name</label>
                                    <input type="text" id="event_name" name="event_name" value="<?= htmlspecialchars($update_event_details["event_name"]) ?>" required placeholder="e.g. Alumni Meet 2026">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description <span class="optional">(optional)</span></label>
                                    <textarea id="description" name="description" rows="3" style="justify-content: start;">
                                        <?= htmlspecialchars($update_event_details["event_desc"] ?? "") ?>
                                    </textarea>
                                </div>

                                <div class="form-group">
                                    <label for="venue">Venue</label>
                                    <input type="text" id="venue" name="venue" value="<?= htmlspecialchars($update_event_details["event_venue"]) ?>" required placeholder="e.g. College Auditorium">
                                </div>

                                <div class="date-time-row">
                                    <div class="form-group">
                                        <label for="event_date">Event Date</label>
                                        <input type="date" id="event_date" value="<?= htmlspecialchars($update_event_details["event_date"]) ?>" name="event_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="event_time">Time <span class="optional">(optional)</span></label>
                                        <input type="time" id="event_time" value="<?= htmlspecialchars($update_event_details["event_time"]) ?>" name="event_time">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="google_link">Google Form / Meet Link <span class="optional">(optional)</span></label>
                                    <input type="url" id="google_link" value="<?= htmlspecialchars($update_event_details["event_additional_links"] ?? "") ?>" name="google_link" placeholder="https://forms.google.com/...">
                                </div>

                                <button type="submit" name="create_event_btn" class="submit-btn">
                                    <?= isset($event_id) ? "Update Event" : ""   ?>
                                </button>
                            </form>

                            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
                        </div>
                    <?php else: ?>
                        <!-- New event creation page -->
                        <div class="form-card">
                            <h1>Create Event</h1>

                            <form id="eventForm" method="post" action="">

                                <div class="form-group">
                                    <label for="event_name">Event Name</label>
                                    <input type="text" id="event_name" name="event_name" required placeholder="e.g. Alumni Meet 2026">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description <span class="optional">(optional)</span></label>
                                    <textarea id="description" name="description" rows="3" placeholder="Short details about the event"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="venue">Venue</label>
                                    <input type="text" id="venue" name="venue" required placeholder="e.g. College Auditorium">
                                </div>

                                <div class="date-time-row">
                                    <div class="form-group">
                                        <label for="event_date">Event Date</label>
                                        <input type="date" id="event_date" name="event_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="event_time">Time <span class="optional">(optional)</span></label>
                                        <input type="time" id="event_time" name="event_time">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="google_link">Google Form / Meet Link <span class="optional">(optional)</span></label>
                                    <input type="url" id="google_link" name="google_link" placeholder="https://forms.google.com/...">
                                </div>

                                <button type="submit" name="create_event_btn" class="submit-btn">
                                    Create Event
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