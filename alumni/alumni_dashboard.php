<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['Enroll_no_alumni'])) {
  header('Location: ../login.php');
  exit();
}

if (!isset($_SESSION['alumni_id'])) {
  header("Location: alumni_dashboard.php");
}


$alumni_login_id = $_SESSION['alumni_id'];

$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no_alumni'];

try {
  $fetch_user_data = "SELECT * FROM alumnimaster WHERE Enrollment_No = ?";
  $fetch_user_stmt = $conn->prepare($fetch_user_data);
  $fetch_user_stmt->bind_param('i', $loggedIn_user);
  $fetch_user_stmt->execute();
  $user_data = $fetch_user_stmt->get_result();

  if ($user_data->num_rows === 1) {
    $user_data_fetched = $user_data->fetch_assoc();
    $_SESSION['alumni_name'] = $user_data_fetched['alumni_name'];
  }
  $fetch_user_stmt->close();
} catch (Exception $e) {
  echo "<script>alert('Error fetching user data');</script>";
}

$alumni_posts = "SELECT * FROM postmaster WHERE created_by = ?";
$alumni_post_stmt = $conn->prepare($alumni_posts);
$alumni_post_stmt->bind_param('i', $alumni_login_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Alumni Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f4f9;
    }

    .main {
      margin-left: 240px;
      padding: 70px;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .avatar {
      width: 80px;
      height: 80px;
      background: #007bff;
      color: white;
      font-size: 30px;
      font-weight: bold;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: auto;
    }

    .text-info {
      height: 12px;
    }

    .text-muted-1 {
      padding-top: 1px;
    }

    .text-primary {
      padding-top: 8px;
    }

    .post-card {
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      padding: 24px;
      margin: 20px auto;
      width: 900px;
      max-width: 900px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: box-shadow 0.3s ease-in-out;
    }

    .post-card:hover {
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    }

    .post-title {
      font-size: 22px;
      font-weight: 700;
      color: #2a2a2a;
      margin-bottom: 16px;
    }

    .post-desc {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .post-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px 40px;
      font-size: 14px;
      color: #333;
      margin-bottom: 20px;
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
      padding: 8px 20px;
      background-color: #007bff;
      color: #ffffff;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease-in-out;
    }

    .apply-btn:hover {
      background-color: #0056b3;
    }

    #post p {
      text-align: left;
      font-size: 16px;
      color: #888;
    }


    .button-group-container {
      display: flex;
      justify-content: left;
      gap: 0;
      margin-top: 20px;
    }

    .btn-custom-left,
    .btn-custom-right {
      padding: 10px 20px;
      border: none;
      font-size: 14px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background 0.3s ease-in-out;
    }

    .btn-custom-left {
      background-color: white;
      border-radius: 25px 0 0 25px;
      color: #333;
    }

    .btn-custom-right {
      background-color: white;
      border-radius: 0 25px 25px 0;
      color: #333;
      border-left: 1px solid #ccc;
    }

    .btn-custom-left:hover,
    .btn-custom-right:hover {
      background-color: #dce3ec;
      color: #000;
    }

    .socialdetails {
      width: 1000px;
      justify-content: center;
    }
  </style>
</head>

<body>

  <div class="d-flex">

    <?php include './sidebar.php' ?>

    <div class="main w-100">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['alumni_name']); ?></h2>
      <p class="text-muted">Here's a quick overview of your profile.</p>

      <!-- User Profile -->
      <div class="row mb-4">
        <div class="row g-4">
          <!-- Left side: Profile card -->
          <div class="col-lg-5">
            <div class="card text-center p-4 h-100">
              <div class="avatar"><?= strtoupper(substr($user_data_fetched['alumni_name'] ?? 'A', 0, 1)) ?></div>
              <h5 class="mt-3"><?= htmlspecialchars($user_data_fetched['alumni_name']) ?></h5>
              <p class="mb-1"><strong>College:</strong> <?= htmlspecialchars($user_data_fetched['alumni_college']) ?></p>
              <p class="mb-1"><strong>Department:</strong> <?= htmlspecialchars($user_data_fetched['alumni_department']) ?></p>
              <p class="mb-1"><strong>City:</strong> <?= $user_data_fetched['alumni_city'] ? htmlspecialchars($user_data_fetched['alumni_city']) : "<p class='text-danger'>Update City</p>" ?></p>

            </div>
          </div>

          <!-- Right side: Info cards -->
          <!-- Right side: Combined Info Cards (2x2 inside one big card) -->
          <div class="col-lg-7">
            <div class="card p-4 h-100">
              <div class="row g-4">
                <!-- New Orders -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fa-solid fa-book-open p-2 m-2 text-primary"></i>
                    <div>
                      <h6 class="mb-0">Bio</h6>
                      <?php
                      if (!empty($user_data_fetched['alumni_bio'])) {
                      ?>
                        <strong><?= htmlspecialchars($user_data_fetched['alumni_bio']); ?></strong>
                        <div class="text-muted small">#Your Bio</div>
                      <?php } else { ?>
                        <p><strong>#YourBio </strong></p>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <!-- Revenue -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fa-solid fa-building p-2 m-2 text-primary"></i>
                    <div>
                      <h6 class="mb-0">Company</h6>

                      <?php
                      if (!empty($user_data_fetched['alumni_company_name'])) {
                      ?>
                        <strong><?= htmlspecialchars($user_data_fetched['alumni_company_name']); ?></strong>
                        <div class="text-muted small">#Your Company</div>
                      <?php } else { ?>
                        <p><strong>#YourCompany </strong></p>
                        <div class="text-muted small">#Your Company</div>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <!-- Support -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <img src="https://cdn-icons-png.flaticon.com/512/5599/5599681.png" width="50px" height="50px" class="p-2">
                    <div>
                      <h6 class="mb-0">Posts</h6>
                      <?php
                      $alumni_post_stmt->execute();
                      $result = $alumni_post_stmt->get_result();
                      $no_post = $result->num_rows;
                      if ($no_post > 0) {
                      ?>
                        <strong><?= htmlspecialchars($no_post) ?></strong>
                      <?php } else { ?>
                        <strong>No post available</strong>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <!-- Joined -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fas fa-clock text-info p-2 m-2"></i>
                    <div>
                      <h6 class="mb-0">Joined</h6>
                      <strong><?= htmlspecialchars(date('Y', strtotime($user_data_fetched['created_at']))) ?></strong>
                      <div class="text-muted small">#Year of Registration</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>

      <div class="button-group-container">
        <button id="hidden_details" class="btn-custom-left">
          <i class="fas fa-plus"></i> Post
        </button>
        <button id="displayed_details" class="btn-custom-right">
          <i class="fas fa-info-circle"></i> Details
        </button>
      </div>

      <!-- Bottom row: GitHub and LinkedIn -->
      <!-- Bottom: GitHub and LinkedIn in horizontal layout -->
      <center>
        <div class="socialdetails" id="socialdetails">
          <div class="row mt-4">
            <!-- GitHub -->
            <div class="col-md-6 mb-3">
              <div class="card p-3 h-100">
                <h6 class="text-primary">GitHub</h6>
                <?php if (!empty($user_data_fetched['alumni_githublink'])): ?>
                  <a href="<?= htmlspecialchars($user_data_fetched['alumni_githublink']) ?>" target="_blank"><?= htmlspecialchars($user_data_fetched['alumni_githublink']) ?></a>
                <?php else: ?>
                  <p class="text-muted">#NoGitHubLink</p>
                <?php endif; ?>
                <p class="text-muted p-2">#Show my projects on</p>
              </div>
            </div>

            <!-- LinkedIn -->
            <div class="col-md-6 mb-3">
              <div class="card p-3 h-100">
                <h6 class="text-info">LinkedIn</h6>
                <?php if (!empty($user_data_fetched['alumni_linkedin'])): ?>
                  <a href="<?= htmlspecialchars($user_data_fetched['alumni_linkedin']) ?>" target="_blank"><?= htmlspecialchars($user_data_fetched['alumni_linkedin']) ?></a>
                <?php else: ?>
                  <p class="text-muted">#NoLinkedInLink</p>
                <?php endif; ?>
                <p class="text-muted p-2">#Show my profile on</p>
              </div>
            </div>
          </div>
        </div>
      </center>

      <?php
      $alumni_post_stmt->execute();
      $result = $alumni_post_stmt->get_result();
      $number_of_post_byUser = $result->num_rows;

      if ($number_of_post_byUser > 0) {
        while ($row = $result->fetch_assoc()) {   ?>
          <div class="d-flex justify-content-center">
            <div class="post-card" style="display: none;" id="post">
              <h2 class="post-title">🚀 <?= htmlspecialchars($row['post_title']) ?></h2>

              <p class="post-desc">
                <?= htmlspecialchars($row['post_desc']) ?>
              </p>

              <div class="post-info-grid">
                <div><strong>📍 Location:</strong> <?= htmlspecialchars($row['post_location']) ?> </div>
                <div><strong>🛠 Skills:</strong> <?= htmlspecialchars($row['post_req_skill']) ?></div>
                <div><strong>🗺 Roadmap:</strong> <?= htmlspecialchars($row['post_ded_roadmap']) ?></div>
                <div><strong>📅 Type:</strong> <?= htmlspecialchars($row['post_job_type']) ?></div>
              </div>

              <div class="card-footer">
                <span>Posted on: <?= htmlspecialchars(date('d-m-Y,l', strtotime($row['post_created_at']))) ?></span>
                <!-- <button class="apply-btn">Apply</button> -->
              </div>
            </div>
          </div>

        <?php }
      } else {  ?>
        <p id="post" style="display: none;">Nothing Posted By User</p>
      <?php  } ?>
      <!-- End your code here -->



    </div>

  </div>
  </div>

  <script src="./script.js"></script>
</body>

</html>