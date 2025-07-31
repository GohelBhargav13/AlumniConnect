<?php
require_once '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['student_id'])) {
    echo "Student ID not provided.";
    exit();
}

$student_id = $_GET['student_id'];

$stmt = $conn->prepare("SELECT * FROM studentmaster WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($student['student_name']) ?> | AlumniConnect</title>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; background-color: #f4f6f9;">
  <div style="display: flex; min-height: 100vh;">
    
    <!-- Sidebar -->
    <div style="width: 250px; background-color: #2c3e50;"></div>
      <?php include './sidebar.php'; ?>

    <!-- Profile Content -->
    <div style="flex: 1; padding: 40px;">
      <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
        
        <!-- Profile Header -->
        <div style="display: flex; align-items: center; gap: 20px;">
          <div style="width: 100px; height: 100px; background-color: #007bff; color: white; font-size: 40px; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <?= strtoupper(substr($student['student_name'], 0, 1)) ?>
          </div>
          <div>
            <h2 style="margin: 0; color: #333;"><?= htmlspecialchars($student['student_name']) ?></h2>
            <p style="margin: 5px 0; color: #666; font-size: 15px;"><?= htmlspecialchars($student['student_department']) ?></p>
          </div>
        </div>

        <!-- Details -->
        <div style="margin-top: 30px;">
          <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; color: #444;">Profile Information</h3>
          <table style="width: 100%; margin-top: 15px;">
            <tr>
              <td style="padding: 8px 0; color: #555; font-weight: bold;">Email:</td>
              <td style="padding: 8px 0; color: #333;"><?= htmlspecialchars($student['student_email']) ?></td>
            </tr>
            <tr>
              <td style="padding: 8px 0; color: #555; font-weight: bold;">Phone:</td>
              <td style="padding: 8px 0; color: #333;"><?= htmlspecialchars($student['student_phone_no']) ?></td>
            </tr>
            <tr>
              <td style="padding: 8px 0; color: #555; font-weight: bold;">City:</td>
              <td style="padding: 8px 0; color: #333;"><?= htmlspecialchars($student['student_city'] ?? 'N/A') ?></td>
            </tr>
          </table>
        </div>

        <!-- Buttons -->
        <!-- <div style="margin-top: 30px; display: flex; gap: 15px;">
          <a href="message.php?to=<?= $student['student_id'] ?>" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 6px;">Message</a>
          <a href="new_connection.php?id=<?= $student['student_id'] ?>" style="padding: 10px 20px; background-color: #0a66c2; color: white; text-decoration: none; border-radius: 6px;">Connect</a>
        </div> -->

      </div>
    </div>
  </div>
</body>
</html>
