<?php
require_once '../utills/db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['alumni_id'])) {
    header("Location: alumni_dashboard.php");
}


$alumni_login_id = $_SESSION['alumni_id'];

$connection_request = "SELECT s.student_id,s.student_name,s.student_department,s.student_college, co.conn_id,co.connection_status 
FROM studentmaster s JOIN connectionmaster co ON s.student_id = co.sender_id WHERE receiver_id = ? AND connection_status = 'pending' ";

$connection_request_stmt = $conn->prepare($connection_request);
$connection_request_stmt->bind_param("i", $alumni_login_id);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept'])) {

    $accepted = 'accepted';
    $sender_id = $_POST['student_id'];
    $accept_request = "UPDATE connectionmaster SET connection_status = ? WHERE sender_id = ?";
    $accept_request_stmt = $conn->prepare($accept_request);
    $accept_request_stmt->bind_param("si", $accepted, $sender_id);

    if ($accept_request_stmt->execute()) {
        $_SESSION['message'] = ["success" => true, "final_msg" => "Now You are friends"];
        header("Location: alumni_new_request.php ");
        exit();
    } else {
        $_SESSION['message'] = ["success" => false, "final_msg" => "Error in updating data"];
        header("Location: alumni_new_request.php ");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Connection | AlumniConnect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .connection-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            text-align: left;
            transition: transform 0.2s ease;
        }

        .connection-card:hover {
            transform: translateY(-4px);
        }

        .connection-header {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .connection-detail {
            color: #555;
            margin-bottom: 10px;
        }

        .btn-accept {
            background-color: #0a66c2;
            color: white;
        }

        .btn-reject {
            background-color: #d93025;
            color: white;
        }

        .no-connection {
            text-align: center;
            margin-top: 100px;
            font-size: 1.2rem;
            color: #777;
        }

        #message {
            text-align: center;
            padding: 10px;
            background-color: #e0ffe0;
            border: 1px solid #b2d8b2;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #2e7d32;
        }
    </style>
</head>

<body>
    <?php include './sidebar.php'; ?>

    <div class="container">
        <h2 class="mt-4 mb-3 text-center">New Connection Requests</h2>
        <?php if (isset($_SESSION['message']) && $_SESSION['message']['success']): ?>
            <p id="message"><?= htmlspecialchars($_SESSION['message']['final_msg']) ?></p>
            <script>
                const message = document.getElementById('message');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 2 * 1000);
            </script>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>


        <?php
        $connection_request_stmt->execute();
        $result = $connection_request_stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="connection-card">
                    <div class="connection-header"><?= htmlspecialchars($row['student_name']) ?></div>
                    <div class="connection-detail">Department: <?= htmlspecialchars($row['student_department']) ?></div>
                    <div class="connection-detail">College: <?= htmlspecialchars($row['student_college']) ?></div>

                    <form action="alumni_new_request.php" method="post" class="mt-3 d-flex gap-2">
                        <input type="hidden" name="student_id" value="<?= htmlspecialchars($row['student_id']) ?>">
                        <button type="submit" name="accept" value="accept" class="btn btn-accept btn-sm" style="background-color: white; border: 1px solid black;">Accept</button>
                        <button type="submit" name="reject" value="reject" class="btn btn-reject btn-sm" style="background-color: white; border: 1px solid black;">Reject</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo "<div class='no-connection'>No new connection requests at the moment.</div>";
        }
        ?>
    </div>

</body>

</html>