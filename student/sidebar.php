<?php 

  if(!isset($_SESSION['Enroll_no'])){
    $errormessage = "Enrollment number is not set";
  }

  $user_enrollment = $_SESSION['Enroll_no'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <style>
    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Sidebar Styling */
    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #0f172a;
      /* Slate-900 */
      color: #f1f5f9;
      /* Slate-100 */
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      justify-content: start;
      padding: 20px 0;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    }

    /* Header Text */
    .sidebar .container {
      text-align: center;
      padding-bottom: 30px;
      border-bottom: 1px solid #334155;
      /* Slate-700 */
    }

    .sidebar h2 {
      font-size: 24px;
      margin-bottom: 5px;
      color: #38bdf8;
      /* Sky-400 */
    }

    .sidebar h4 {
      font-size: 16px;
      font-weight: normal;
      color: #cbd5e1;
      /* Slate-300 */
    }

    /* Nav Menu */
    .sidebar nav {
      display: flex;
      flex-direction: column;
      margin-top: 30px;
    }

    .sidebar nav a {
      text-decoration: none;
      color: #cbd5e1;
      padding: 12px 20px;
      transition: background 0.3s, color 0.3s;
      font-size: 15px;
    }

    .sidebar nav a:hover {
      background-color: #1e293b;
      /* Slate-800 */
      color: #ffffff;
    }
/* 
    .sidebar nav a.active {
      background-color: #38bdf8;
      color: #0f172a;
      font-weight: bold;
      border-radius: 0 50px 50px 0;
    } */
  </style>
</head>

<body>

  <aside class="sidebar">
    <div class="container">
      <h2>AlumniConnect</h2>
      <h4>Student Dashboard</h4>
    </div>

    <nav>
      <a href="./student_dashboard.php">Dashboard</a>
      <a href="./student_view_post.php">Post View</a>
      <a href="#">Articles</a>
      <a href="#">Collections</a>
      <a href="./edit_student_profile.php?edit=<?= htmlspecialchars($user_enrollment) ?>">Edit Profile</a>
      <a href="../logout.php">Logout</a>
    </nav>
  </aside>

</body>

</html>