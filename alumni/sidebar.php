<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['alumni_id'])) {
  $errormessage = 'Id was not found';
  exit();
}

$alumni_id = $_SESSION['alumni_id'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f0f2f5;
      color: #333;
      box-sizing: border-box;
      display: flex;
      /* Make body a flex container to house the dashboard */
      min-height: 100vh;
    }

    .sidebar {
      height: 100vh;
      background: #2c3e50;
      padding-top: 40px;
      position: fixed;
      width: 240px;
      color: white;
    }

    .sidebar a {
      color: #ecf0f1;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
      transition: 0.3s;
      font-weight:600;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #34495e;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar h4 {
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
    }
  </style>
</head>

<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5;">
  <div class="p-2 m-2">
    <?php if (!empty($errormessage)) { ?>
      <p><?= htmlspecialchars($errormessage) ?></p>
    <?php } ?>
  </div>
  <div class="sidebar">
    <h4 class="text-center">AlumniConnect</h4>
    <p class="text-center">Alumni Dashboard</p>
    <hr class="text-white">
    <a href="./alumni_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="./alumni_create_post.php"><i class="fas fa-briefcase"></i> Create Post</a>
    <a href="./alumni_post_view.php"><i class="fas fa-newspaper"></i> View Posts</a>
    <a href="./alumni_new_request.php"><i class="fa-solid fa-bell"></i> New Connection</a>
     <a href="./show_friends.php"><i class="fa-solid fa-user-group"></i></i> Friends </a>
    <a href="./edit_alumni_profile.php?edit=<?= htmlspecialchars($alumni_id) ?>"><i class="fas fa-user-edit"></i> Edit Profile</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</body>

</html>