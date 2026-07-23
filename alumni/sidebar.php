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
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #ffffff;
      color: #2b2f31;
      box-sizing: border-box;
      display: flex;
      /* Make body a flex container to house the dashboard */
      min-height: 100vh;
    }

    .sidebar {
      height: 100vh;
      background: #2E75B6;
      padding-top: 40px;
      position: fixed;
      width: 240px;
      color: white;
      box-sizing: border-box;
      overflow-y: auto;
    }

    .sidebar a {
      color: #ffffff;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
      transition: 0.3s;
      font-weight: 600;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #1F5A94;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar h4 {
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      color: #ffffff;
    }

    .sidebar p {
      color: #e7f0f9;
    }

    .sidebar hr {
      border-color: rgba(255, 255, 255, 0.4);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 768px) {
      .sidebar {
        position: static;
        height: auto;
        width: 100%;
        padding: 16px 0;
        display: flex;
        flex-direction: column;
      }

      .sidebar h4,
      .sidebar p,
      .sidebar hr {
        display: none;
      }

      .sidebar a {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        font-size: 13px;
      }
    }

    @media (max-width: 500px) {
      .sidebar {
        flex-direction: column;
      }

      .sidebar a {
        width: 100%;
        text-align: left;
        padding: 10px 20px;
      }
    }
  </style>
</head>

<body>
  <div class="p-2 m-2">
    <?php if (!empty($errormessage)) { ?>
      <p><?= htmlspecialchars($errormessage) ?></p>
    <?php } ?>
  </div>
  <div class="sidebar">
    <h4 class="text-center">AlumniConnect</h4>
    <p class="text-center">Alumni Dashboard</p>
    <hr class="text-white">
    <a href="./landing.php"><i class="fas fa-home"></i>Home</a>
    <a href="./alumni_dashboard.php"><i class="fa-solid fa-circle-info"></i> Dashboard</a>
    <a href="./alumni_community_post.php"><i class="fas fa-briefcase"></i> Community Post</a>
    <a href="./alumni_post_view.php"><i class="fa-solid fa-paste"></i> Posts</a>
    <a href="./edit_alumni_profile.php?edit=<?= htmlspecialchars($alumni_id) ?>"><i class="fas fa-user-edit"></i> Edit Profile</a>
    <a href="./change_password.php"><i class="fa-solid fa-lock"></i> Change password</a>
  </div>
</body>

</html>