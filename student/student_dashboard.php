<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['Enroll_no'])) {
  header('Location: ../login.php');
  exit();
}
$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no'];

try {

  $fetch_user_data = "SELECT * FROM studentmaster WHERE Enrollment_no = ?";
  $fetch_user_stmt = $conn->prepare($fetch_user_data);
  $fetch_user_stmt->bind_param('i', $loggedIn_user);
  $fetch_user_stmt->execute();
  $user_data = $fetch_user_stmt->get_result();

  if ($user_data->num_rows === 1) {
    while ($row = $user_data->fetch_assoc()) {
      $user_data_fetched = $row;
    }
  }
  $fetch_user_stmt->close();
} catch (Exception $th) {
  echo "<script>alert('Data not found of the user',$th)</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Student Dashboard | AlumniConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --sidebar-bg: #294460;
      --sidebar-active: #2563eb;
      --sidebar-text: #f2f6fa;
      --main-bg: #f3f7fa;
      --card-bg: #fff;
      --primary: #2563eb;
      --text-dark: #18344b;
      --border: #e0e9f3;
      --shadow: 0 6px 30px #29446018;
      --accent: #2563eb;
      --text-light: #f2f6fa;
    }

    body {
      margin: 0;
      background: var(--main-bg);
      font-family: 'Segoe UI', Arial, sans-serif;
    }
    .main-content {
      flex: 1;
      padding: 40px 36px;
      display: flex;
      flex-direction: column;
      gap: 24px;
      align-items: center;
    }

    .user-info-container {
      background: var(--card-bg);
      box-shadow: 0 3px 18px #29446013;
      border-radius: 14px;
      padding: 36px 38px 22px 38px;
      max-width: 470px;
      width: 100%;
      margin-bottom: 32px;
      display: flex;
      flex-direction: column;
      align-items: center;
      border: 1px solid var(--border-light);
    }

    .user-name {
      font-size: 1.4em;
      font-weight: bold;
      color: var(--accent);
      margin-bottom: 6px;
    }

    .user-bio {
      color: #607590;
      font-size: 1em;
      margin-bottom: 15px;
      text-align: center;
    }

    .user-details {
      color: var(--text-dark);
      font-size: 0.97em;
      margin-bottom: 16px;
      text-align: center;
    }

    .skills-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-bottom: 6px;
    }

    .skill-tag {
      background: var(--tag-bg);
      color: var(--tag-color);
      padding: 4px 12px;
      font-size: 0.97em;
      border-radius: 16px;
      border: 1px solid #e0e9f3;
      font-weight: 500;
    }

    .dashboard-title {
      font-size: 2em;
      font-weight: bold;
      color: var(--text-dark);
      letter-spacing: 1px;
    }

    .cards {
      display: flex;
      gap: 22px;
      flex-wrap: wrap;
      width: 100%;
      justify-content: center;
    }

    .card {
      background: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 2px 18px #29446017;
      padding: 30px 32px;
      flex: 1 0 185px;
      min-width: 250px;
      max-width: 220px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .card-title {
      color: var(--accent);
      font-weight: bold;
      margin-bottom: 8px;
      font-size: 1em;
    }

    .card-value {
      font-size: 0.8em;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .card-desc {
      font-size: 0.95em;
      color: #6b7e92;
    }

    /* Responsive Design */
    @media (max-width: 900px) {
      .dashboard {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        flex-direction: row;
        gap: 0;
        padding: 18px 10px;
      }

      .sidebar h2 {
        display: none;
      }

      .sidebar nav {
        display: flex;
        gap: 10px;
      }

      .main-content {
        padding: 16px 4vw;
      }

      .cards {
        flex-direction: column;
        gap: 14px;
      }

      .user-info-container {
        padding: 22px 8vw;
      }
    }
  </style>
</head>

<body>
  <div class="dashboard">
    <?php include './sidebar.php' ?>
    <main class="main-content">
      <div class="dashboard-title">Welcome to Your Dashboard</div>
      <div class="user-info-container">
        <div class="user-name"><?= htmlspecialchars($user_data_fetched['student_name']); ?></div>
        <div class="user-bio">
          <?php
          if (!empty($user_data_fetched['student_bio'])) {
            echo $user_data_fetched['student_bio'];
          } else {
            echo "#BioUpdate";
          }
          ?>
        </div>
        <div class="user-details">
          <?php
          $city_set = false;
          if (!empty($user_data_fetched['student_city'])) {
            $city_set = true;
          } else {
            $city_set = false;
          }
          ?>
          Joined: <?= htmlspecialchars(date('Y', strtotime($user_data_fetched['created_at']))) ?> &nbsp;|&nbsp; <?= $city_set ? htmlspecialchars($user_data_fetched['student_city']) : '#cityname' ?>, IN
        </div>
        <hr style="border: 1px solid black; width: 80%; margin: 10px auto;">

        <!-- <div class="skills-list">
          <span class="skill-tag">JavaScript</span>
          <span class="skill-tag">NodeJS</span>
          <span class="skill-tag">ExpressJS</span>
          <span class="skill-tag">MongoDB</span>
          <span class="skill-tag">Docker</span>
        </div> -->

        <div class="college_name" style="padding-left: 5px;">
          <p><b>College Name:</b></p>
          <?php if (!empty($user_data_fetched['student_college'])) { ?>
            <p id="college_name"><?= htmlspecialchars($user_data_fetched['student_college']) ?></p>
          <?php } else { ?>
            <p>#collegeName</p>
          <?php } ?>
        </div>
        <hr style="border: 1px solid black; width: 80%; margin: 10px auto;">
        <div class="dep_name" style="margin-left: 50px; padding-left: 5px;">
          <p><b>Department:</b></p>
          <?php if (!empty($user_data_fetched['student_department'])) { ?>
            <p id="dep_name"><?= htmlspecialchars($user_data_fetched['student_department']) ?></p>
          <?php } else { ?>
            <p>#depName</p>
          <?php } ?>
        </div>
      </div>
      <section class="cards">
        <div class="card">
          <div class="card-title">Github</div>
          <div class="card-value">
            <?php if (!empty($user_data_fetched['student_github'])) { ?>
              <a href="<?= htmlspecialchars($user_data_fetched['student_github']) ?>" target="_blank"><?= htmlspecialchars($user_data_fetched['student_github']) ?></a>
            <?php } else { ?>
              <div class="card-value">#GITHUBLink</div>
            <?php  } ?>
          </div>
          <div class="card-desc">Show My Skills On</div>
        </div>
        <div class="card">
          <div class="card-title">LinkedIn</div>
          <div class="card-value">
            <?php if (!empty($user_data_fetched['student_linkedIn'])) { ?>
              <a href="<?= htmlspecialchars($user_data_fetched['student_linkedIn']) ?>" target="_blank"><?= htmlspecialchars($user_data_fetched['student_linkedIn']) ?></a>
            <?php } else { ?>
              <div class="card-value">#LINKEDINLink</div>
            <?php  } ?>
          </div>
          <div class="card-desc">Show My Profile On</div>
        </div>
      </section>
      <!-- Add more dashboard sections as needed -->
    </main>
  </div>
</body>

</html>