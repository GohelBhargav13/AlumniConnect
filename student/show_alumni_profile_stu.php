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
      $mail->Body = '
<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
  <tr>
    <td>
      <table cellpadding="0" cellspacing="0" width="600" align="center" style="background-color: #ffffff; border-radius: 6px; padding: 20px; border: 1px solid #ddd;">
        <tr>
          <td style="text-align: center; padding-bottom: 20px;">
            <h2 style="color: #1a191fff; font-size: 24px;">📩 Connection Request</h2>
          </td>
        </tr>
        <tr>
          <td style="color: #050411ff; font-size: 16px; line-height: 1.5;">
            <p>Hello <b style="color: #0073e6;">' . $alumni_details['alumni_name'] . '</b>,</p>
            <p>You have received a connection request from <b style="color: #28a745;">' . $student_details['student_name'] . '</b>.</p>
            <p style="margin-top: 20px;">Please log in to your account to view and respond to this request.</p>
          </td>
        </tr>
        <tr>
          <td style="padding-top: 30px; text-align: center;">
            <a href="../alumni/alumni_dashboard.php" style="background-color: #007bff; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;">View Request</a>
          </td>
        </tr>
        <tr>
          <td style="padding-top: 30px; font-size: 12px; color: #999999; text-align: center;">
            <p>This is an automated message. Please do not reply directly to this email.</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>';

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
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      overflow: hidden;
      
    }

    .sidebar {
      width: 250px;
      background-color: #0f172a;
      color: white;
      padding: 20px;
    }

    .main-content {
      flex: 1;
      overflow-y: auto;
      padding: 245px;
      padding-right: 20px;
      background-color: #f1f5f9;
      align-items: center;
    }

    .profile-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-name {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin: 15px 0 5px;
      color: #0073e6;
    }

    .profile-tagline {
      text-align: center;
      font-size: 16px;
      color: #555;
      margin-bottom: 20px;
    }

    .btn-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .btn {
      padding: 10px 16px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-weight: bold;
    }

    .btn-connect {
      background-color: #0073e6;
      color: white;
    }

    .btn-message {
      background-color: #ffffff;
      border: 2px solid #0073e6;
      color: #0073e6;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
  <?php include './sidebar.php' ?>
  <div class="main-content">
    <div class="profile-card">


      <div class="profile-name">
        <?= htmlspecialchars($alumni_details['alumni_name'] ?? '') ?>
      </div>
      <div class="profile-tagline"><strong>Bio:</strong> <?= htmlspecialchars($alumni_details['alumni_bio'] ?? 'N/A') ?></div>
      <div class="profile-tagline"><strong>College:</strong> <?= htmlspecialchars($alumni_details['alumni_college'] ?? 'N/A') ?></div>
      <div class="profile-tagline"><strong>Department:</strong> <?= htmlspecialchars($alumni_details['alumni_department'] ?? 'N/A') ?></div>
      <div class="profile-tagline"><strong>Pass Out Year:</strong> <?= htmlspecialchars($alumni_details['alumni_pass_year'] ?? 'N/A') ?></div>


      <div class="container" style="text-align: center;">
        <?php if (isset($_SESSION['message'])): ?>
          <p id="message"><?= htmlspecialchars($_SESSION['message']['final_msg']); ?></p>
          <script>
            const message = document.getElementById('message');
            setTimeout(() => {
              message.style.display = 'none';
            }, 2 * 1000)
          </script>
          <?php unset($_SESSION['message']) ?>
        <?php endif; ?>

        <form action="./show_alumni_profile_stu.php?alumni_id=<?= urldecode($alumni_id_get) ?>" method="post">


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
            <span class="badge text-bg-primary">Pending</span>
          <?php } elseif ($final_status === 'accepted') { ?>
            <span class="badge text-bg-success">Friends</span>
          <?php } else { ?>
            <button class="btn btn-message" type="submit" name="connect_req">Connect</button>
          <?php } ?>

        </form>
      </div>
</body>


</html>