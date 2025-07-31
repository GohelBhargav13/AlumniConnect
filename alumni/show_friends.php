<?php
require_once '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['alumni_id'])) {
    $_SESSION['message'] = ["success" => false, "final_msg" => "student id is not set"];
    header("Location:../login.php");
    exit();
}

$alumni_id = $_SESSION['alumni_id'];

$fetch_friends = "SELECT s.*, c.conn_id 
    FROM studentmaster s  
    JOIN connectionmaster c ON c.sender_id = s.student_id 
    WHERE receiver_id = ? AND connection_status = 'accepted'";

$fetch_friends_stmt = $conn->prepare($fetch_friends);
$fetch_friends_stmt->bind_param("i", $alumni_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Friends | AlumniConnect</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5;">
  <div style="display: flex; min-height: 100vh;">

    <!-- Sidebar -->
    <div style="width: 225px; background-color: #2c3e50; height: 100vh;"></div>
     <?php include './sidebar.php'; ?>

    <!-- Main Content -->
    <div style="flex: 1; padding: 40px;">
      <h2 style="margin-bottom: 30px; font-size: 28px; color: #2c3e50;">My Connections</h2>

      <div style="display: flex; flex-wrap: wrap; gap: 25px;">
        <?php
        $fetch_friends_stmt->execute();
        $fethced_friends = $fetch_friends_stmt->get_result();

        if ($fethced_friends->num_rows > 0) {
          while ($row = $fethced_friends->fetch_assoc()) {
            $initial = strtoupper(substr($row['student_name'] ?? 'N', 0, 1));
        ?>
          <!-- Modern Friend Card -->
          <div style="background-color: white; width: 280px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 20px; display: flex; flex-direction: column; align-items: center;">
            <div style="width: 90px; height: 90px; background-color: #007bff; color: white; font-size: 36px; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
              <?= $initial ?>
            </div>
            <div style="text-align: center;">
              <h3 style="margin: 0; font-size: 20px; color: #333;"><?= htmlspecialchars($row['student_name']) ?></h3>
              <p style="margin-top: 6px; color: #666; font-size: 14px;"><?= htmlspecialchars($row['student_department']) ?></p>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
              <a href="show_student_profile.php?student_id=<?= urlencode($row['student_id']) ?>" style="padding: 8px 16px; font-size: 14px; border-radius: 6px; background-color: #0a66c2; color: white; text-decoration: none; font-weight: 500;">View</a>
              <button style="padding: 8px 16px; font-size: 14px; border-radius: 6px; background-color: #28a745; color: white; border: none; font-weight: 500; cursor: pointer;">Message</button>
            </div>
          </div>
        <?php
          }
        } else {
          echo "<p style='color: #888;'>No connections found.</p>";
        }
        ?>
      </div>
    </div>
  </div>
</body>
</html>
