<?php
require_once '../utills/db_conn.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$alumni_id_get = $_SESSION['alumni_id_get'] ?? null;
$alumni_details = $_SESSION['user_data_full']['data'] ?? null;
$student_details = $_SESSION['user_data_full_stu']['data_stu'] ?? null;

// === GET REQUEST: Load alumni and student data ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['alumni_id'])) {
    $alumni_id_get = urldecode($_GET['alumni_id']);
    $_SESSION['alumni_id_get'] = $alumni_id_get;

    // Fetch alumni details
    $find_alumni_sql = "SELECT * FROM alumnimaster WHERE alumni_id = ?";
    $find_alumni_sql_stmt = $conn->prepare($find_alumni_sql);
    $find_alumni_sql_stmt->bind_param('i', $alumni_id_get);
    if ($find_alumni_sql_stmt->execute()) {
        $final_data = $find_alumni_sql_stmt->get_result();
        $alumni_details = $final_data->fetch_assoc();
        $_SESSION['user_data_full'] = ["data" => $alumni_details];
    }

    // Fetch student details
    $find_student_sql = "SELECT * FROM studentmaster WHERE student_id = ?";
    $find_student_sql_stmt = $conn->prepare($find_student_sql);
    $find_student_sql_stmt->bind_param('i', $student_id);
    if ($find_student_sql_stmt->execute()) {
        $final_data = $find_student_sql_stmt->get_result();
        $student_details = $final_data->fetch_assoc();
        $_SESSION['user_data_full_stu'] = ["data_stu" => $student_details];
    }
}

// === POST REQUEST: Send connection request ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['connect_req'])) {

    // Validate required session data
    $alumni_id_get = $_SESSION['alumni_id_get'] ?? null;
    $student_details = $_SESSION['user_data_full_stu']['data_stu'] ?? null;
    $alumni_details = $_SESSION['user_data_full']['data'] ?? null;

    if (!$alumni_id_get || !$student_details || !$alumni_details) {
        $_SESSION['message'] = ["success" => false, "final_msg" => "Missing data for connection request."];
        header("Location: student_dashboard.php");
        exit();
    }

    // Insert connection request
    $insert_connection_req = "INSERT INTO connectionmaster (sender_id, receiver_id) VALUES (?, ?)";
    $insert_connection_req_stmt = $conn->prepare($insert_connection_req);
    $insert_connection_req_stmt->bind_param("ii", $student_id, $alumni_id_get);
    $insert_connection_req_stmt->execute();

    if ($insert_connection_req_stmt->affected_rows === 1) {
        $mail = new PHPMailer(true);

        try {
            // Email setup
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gohelbhargav401@gmail.com'; // FIXED: Removed extra spaces
            $mail->Password = 'aqknaoglmxclkvct'; // ⚠️ Use app password or store in env file
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($student_details['student_email'], $student_details['student_name']);
            $mail->addAddress($alumni_details['alumni_email'], $alumni_details['alumni_name']);
            $mail->addReplyTo('replyto@example.com', 'Reply');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Connection Request via AlumniConnect';
            $mail->Body = "<p>Hello <b>{$alumni_details['alumni_name']}</b>,</p>
                           <p>You have received a connection request from <b>{$student_details['student_name']}</b>.</p>";
            $mail->AltBody = "Connection request from {$student_details['student_name']}.";

            if ($mail->send()) {
                $_SESSION['message'] = ["success" => true, "final_msg" => "Connection request sent"];
                header("Location: show_alumni_profile_stu.php?alumni_id=" . urlencode($alumni_id_get));
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['message'] = [
                "success" => false,
                "final_msg" => "Mailer Error: {$mail->ErrorInfo}"
            ];
            header("Location: show_alumni_profile_stu.php?alumni_id=" . urlencode($alumni_id_get));
            exit();
        }
    } else {
        $_SESSION["message"] = ["success" => false, "final_msg" => "Connection request not sent."];
        header("Location: show_alumni_profile_stu.php?alumni_id=" . urlencode($alumni_id_get));
        exit();
    }

    $insert_connection_req_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni profile | AlumniConnect</title>
</head>

<body>
    <?php include './sidebar.php' ?>

    <div class="container" style="text-align: center;">
        <?php if (isset($_SESSION['message'])): ?>
            <p><?= htmlspecialchars($_SESSION['message']['final_msg']); ?></p>
        <?php endif; ?>

        <form action="./show_alumni_profile_stu.php?alumni_id=<?= urldecode($alumni_id_get) ?>" method="post">
            <p><?= htmlspecialchars($_SESSION['user_data_full']['data']['alumni_name'] ?? ''); ?></p>

            <?php
            $select_connection_request = "SELECT connection_status FROM connectionmaster WHERE sender_id = ? AND receiver_id = ?";
            $select_connection_request_stmt = $conn->prepare($select_connection_request);
            $select_connection_request_stmt->bind_param("ii", $student_id, $alumni_id_get);
            $select_connection_request_stmt->execute();
            $alumni_conn_result = $select_connection_request_stmt->get_result();

            $final_row = $alumni_conn_result->fetch_assoc();
            $final_status = $final_row['connection_status'] ?? null;
            ?>

            <?php if ($final_status === 'pending') { ?>
                <p>Pending</p>
            <?php } elseif ($final_status === 'accepted') { ?>
                <p>Friends</p>
            <?php } else { ?>
                <p>Connect</p>
            <?php } ?>

        </form>

        <?php unset($_SESSION['message']); ?>
    </div>
</body>


</html>