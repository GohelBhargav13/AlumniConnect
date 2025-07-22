<?php
require_once '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$post_fetch = "SELECT p.*, am.alumni_name
FROM postmaster as p 
JOIN alumnimaster as am ON am.alumni_id = p.created_by";
$fetched_result = $conn->query($post_fetch);

if (!isset($_SESSION['alumni_id'])){
    header('Location:./alumni_dashboard.php');
}

$alumni_id = $_SESSION['alumni_id']
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Alumni Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      height: 100vh;
      background: #2c3e50;
      padding-top: 40px;
      position: fixed;
      width: 240px;
      color: white;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 24px;
      font-weight: bold;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 10px;
      text-decoration: none;
      color: white;
      border-radius: 6px;
      margin-bottom: 10px;
      transition: 0.2s;
    }

    .sidebar a:hover, .sidebar a.active {
      background-color: #34495e;
    }

    .sidebar i {
      margin-right: 12px;
      font-size: 16px;
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
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>AlumniConnect</h2>
    <a href="./alumni_dashboard.php"><i class="bi bi-house-door-fill"></i> Dashboard</a>
    <a href="./alumni_create_post.php"><i class="bi bi-briefcase-fill"></i> Create Post</a>
    <a href="./alumni_post_view.php" class="active"><i class="bi bi-journal-richtext"></i> View Posts</a>
    <a href=""><i class="bi bi-journal-bookmark-fill"></i> Collections</a>
    <a href="./edit_alumni_profile.php?edit=<?= $alumni_id ?>"><i class="bi bi-person-lines-fill"></i> Edit Profile</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- <a href="./alumni_dashboard.php"><button class="apply-btn" style="margin-bottom: 20px;">Back</button></a> -->

    <?php if ($fetched_result->num_rows > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($fetched_result)): ?>
        <div class="post-card">
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
            <button class="apply-btn">Apply</button>
          </div>
          <div class="card-footer">
            <span>Posted by: <?= htmlspecialchars($row['alumni_name']) ?></span>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-post">No Post Available</div>
    <?php endif; ?>
  </div>
</body>
</html>
