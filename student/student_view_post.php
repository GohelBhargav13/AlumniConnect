<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Fetch all posts
$fetch_all_post = "SELECT p.*, am.alumni_name,am.alumni_email 
    FROM postmaster as p 
    JOIN alumnimaster as am ON am.alumni_id = p.created_by 
    ORDER BY post_created_at DESC";
$result = $conn->query($fetch_all_post);

// Get student ID
$student_id = $_SESSION['student_id'] ?? 0;
$student_name = $_SESSION['student_name'] ?? '';
$student_enrollment_mail =  $_SESSION['Enroll_no'] ?? 0;

// Fetch posts already applied by this student
$applied_post_ids = [];

if ($student_id) {
  $applied_query = "SELECT post_id FROM applystudentmaster WHERE student_id = ?";
  $stmt = $conn->prepare($applied_query);
  $stmt->bind_param("i", $student_id);
  $stmt->execute();
  $applied_result = $stmt->get_result();
  while ($row = $applied_result->fetch_assoc()) {
    $applied_post_ids[] = $row['post_id'];
  }
  $stmt->close();
}

$student_details = [];

$search_student = "SELECT * FROM studentmaster WHERE student_id = ?";
$search_student_stmt = $conn->prepare($search_student);
$search_student_stmt->bind_param("i", $student_id);
$search_student_stmt->execute();

$search_student_result = $search_student_stmt->get_result();
while ($row2 = $search_student_result->fetch_assoc()) {
  $student_email = $row2['student_email'] ?? '';
  $student_gra = $row2['student_add_year'] ?? 0;
  $student_linkedIn = $row2['student_linkedIn'] ?? '';
}



// Handle Apply POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['applybtn'])) {
  $post_id = (int) $_POST['applybtn'];

  $alumni_name_mail = $_POST['alumnihidden'] ?? '';
  $post_title = $_POST['post_name'] ?? '';

  if (!$student_id) {
    $_SESSION['message'] = ["success" => false, "final_msg" => "Student not logged in"];
    header("Location: student_view_post.php");
    exit();
  }

  // Check again before inserting (safety)
  $check_query = "SELECT * FROM applystudentmaster WHERE student_id = ? AND post_id = ?";
  $check_stmt = $conn->prepare($check_query);
  $check_stmt->bind_param("ii", $student_id, $post_id);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();

  if ($check_result->num_rows > 0) {
    $_SESSION['message'] = ["success" => false, "final_msg" => "You already applied to this post"];
  } else {
    $insert_sql = "INSERT INTO applystudentmaster (student_id, post_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $student_id, $post_id);
    if ($insert_stmt->execute()) {
      $_SESSION['message'] = ["success" => true, "final_msg" => "Applied Successfully"];
    } else {
      $_SESSION['message'] = ["success" => false, "final_msg" => "Something went wrong"];
    }
    $insert_stmt->close();
  }

  require '../vendor/autoload.php'; // Adjust based on your installation method

  $mail = new PHPMailer(true); // Enable exceptions

  // SMTP Configuration
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com'; // Your SMTP server
  $mail->SMTPAuth = true;
  $mail->Username = 'gohelbhargav401@gmail.com  '; // Your Mailtrap username
  $mail->Password = 'aqknaoglmxclkvct'; // Your Mailtrap password
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  // Sender and recipient settings
  $mail->setFrom('gohelbhargav401@gmail.com', 'From AlumniConnect Team');
  $mail->addAddress($_POST['alumniemailhidden'], $_POST['alumnihidden']);

  // Sending plain text email
  $mail->isHTML(true); // Set email format to plain text
  $mail->Subject = 'New Application Received for Your Job/Internship Post';
  $mail->Body = "<html>
  <body style='font-family: Segoe UI, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;'>
    <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;'>
      
      <div style='background-color: #004080; color: #ffffff; padding: 15px; text-align: center; font-size: 20px; font-weight: bold;'>
        AlumniConnect Notification
      </div>

      <div style='padding: 20px; color: #333333; font-size: 16px; line-height: 1.6;'>
        <p>Dear <span style='font-weight: bold; color: #004080;'>$alumni_name_mail</span>,</p>

        <p>Greetings from <strong>AlumniConnect</strong>!</p>

        <p>We are pleased to inform you that a student has shown interest in your job/internship post.</p>
         <p style='color: white; font-weight: bold; margin: 10px 0;'>Post Title:</p>
         <p style='color: white; margin-top: 0;'>$post_title</p>

        <p style='margin-top: 20px; font-weight: bold;'>Applicant's Details:</p>
        <ul style='padding-left: 20px;'>
          <li><strong>Name:</strong> $student_name</li>
          <li><strong>Enrollment No:</strong> $student_enrollment_mail</li>
          <li><strong>Email ID: $student_email</strong> </li>
          <li><strong>Graduation Year: $student_gra</strong> </li>
          <li><strong>Linked In: <a href='$student_linkedIn'>$student_linkedIn</a></strong> </li>
        </ul>

        <p>You can review the applicant’s profile and connect with them directly for further discussion.</p>

        <p>Thank you for your valuable contribution in helping students through <strong>AlumniConnect</strong>.</p>

        <p>Best regards,<br>
        <strong>Team AlumniConnect</strong><br>
        Bridging Students and Alumni</p>
      </div>

      <div style='text-align: center; font-size: 14px; color: #888888; background-color: #f1f1f1; padding: 10px;'>
        © " . date('Y') . " AlumniConnect. All rights reserved.
      </div>

    </div>
  </body>
</html>
";

  // Send the email
  if (!$mail->send()) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  } else {
    echo "<script>alert('Message has been sent')</script>";
  }

  $check_stmt->close();
  header("Location: student_view_post.php"); // prevent resubmission
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Post-View | AlumniConnect</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      display: flex;
    }

    /* Main Content */
    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
      width: calc(100% - 250px);
    }

    .post-card {
      background-color: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 12px;
      padding: 24px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
      transition: 0.3s ease-in-out;
    }

    .post-card:hover {
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    }

    .post-title {
      font-size: 20px;
      font-weight: 600;
      color: #2a2a2a;
      margin-bottom: 10px;
    }

    .post-desc {
      font-size: 14px;
      color: #555;
      margin-bottom: 15px;
    }

    .post-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px 20px;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      color: #666;
      border-top: 1px solid #eaeaea;
      padding-top: 12px;
      margin-top: 10px;
    }

    .apply-btn {
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      font-size: 13px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s ease-in-out;
    }

    .apply-btn:hover {
      background-color: #0056b3;
    }

    .no-post {
      text-align: center;
      font-size: 16px;
      color: #888;
    }
  </style>
</head>

<body>
  <?php include './sidebar.php' ?>
  <!-- Main Content -->
  <div class="main-content">
    <!-- <a href="./alumni_dashboard.php"><button class="apply-btn" style="margin-bottom: 20px;">Back</button></a> -->
    <h2 style="text-align: center; padding:10px; margin-bottom: 15px; font-size: larger;" class="badge badge-primary">Post View</h2>
    <form action="student_view_post.php" method="post">
      <?php if (isset($_SESSION['message'])): ?>
        <p id="message"><?= htmlspecialchars($_SESSION['message']['final_msg']) ?></p>
        <script>
          const message = document.getElementById('message');
          setTimeout(() => {
            message.innerText = '';
          }, 2000);
        </script>
        <?php unset($_SESSION['message']); ?>
      <?php endif; ?>

      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="post-card">
            <input type="hidden" name="post_name" value="<?= htmlspecialchars($row['post_title'])?>">
            <h2 class="post-title">🚀 <?= htmlspecialchars($row['post_title']) ?></h2>
            <p class="post-desc"><?= htmlspecialchars($row['post_desc']) ?></p>

            <div class="post-info-grid">
              <div><strong>📍 Location:</strong> <?= htmlspecialchars($row['post_location']) ?></div>
              <div><strong>🛠 Skills:</strong> <?= htmlspecialchars($row['post_req_skill']) ?></div>
              <div><strong>🗺 Roadmap:</strong> <?= htmlspecialchars($row['post_ded_roadmap']) ?></div>
              <div><strong>📅 Type:</strong> <?= htmlspecialchars($row['post_job_type']) ?></div>
            </div>

            <div class="card-footer">
              <span>Posted on: <?= htmlspecialchars(date('d-m-Y, l', strtotime($row['post_created_at']))) ?></span>
            </div>
            <div class="card-footer">
              <input type="hidden" name="alumnihidden" value="<?= htmlspecialchars($row['alumni_name']) ?>">
              <input type="hidden" name="alumniemailhidden" value="<?= htmlspecialchars($row['alumni_email']) ?>">
              <span>Posted by: <?= htmlspecialchars($row['alumni_name']) ?></span>
            </div>
            <?php if (in_array($row['post_id'], $applied_post_ids)): ?>
              <p class="text-success">✅ Already Applied</p>
            <?php else: ?>
              <button class="apply-btn mt-2" name="applybtn" value="<?= $row['post_id'] ?>">Apply</button>
            <?php endif; ?>


          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-post">No Post Available</div>
      <?php endif; ?>
  </div>
  </form>
</body>

</html>