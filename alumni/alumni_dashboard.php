<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($conn)) {
  die("Database connection is not established.");
}

if (!isset($_SESSION['Enroll_no_alumni'])) {
  header('Location: ../login.php');
  exit();
}

if (!isset($_SESSION['alumni_id'])) {
  header("Location: ../login.php");
  exit();
}

$alumni_login_id = $_SESSION['alumni_id'];
$alumni_email = $_SESSION['email'] ?? '';
$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no_alumni'];

try {
  // make a join with alumni_student_master and alumni_profile for fetching the data of logged in user
  $fetch_query = "
      SELECT am.alumni_id, am.alumni_name, am.email, am.enrollment_no, am.branch, am.created_at,
      ap.alumni_phone_no, ap.alumni_company, ap.alumni_designation, ap.alumni_city,ap.alumni_batch,
      ap.alumni_github_link, ap.alumni_linkedin_link
      FROM alumni_student_master AS am
      LEFT JOIN alumni_profile AS ap ON am.alumni_id = ap.alumni_id
      WHERE am.email = ? OR am.enrollment_no = ?
    ";
  $fetch_user_stmt = $conn->prepare($fetch_query);
  $fetch_user_stmt->bind_param('ss', $alumni_email, $loggedIn_user);
  $fetch_user_stmt->execute();
  $user_data = $fetch_user_stmt->get_result();

  if ($user_data->num_rows === 1) {
    $user_data_fetched = $user_data->fetch_assoc();
    $_SESSION['alumni_name'] = $user_data_fetched['alumni_name'];
  }
  $fetch_user_stmt->close();
} catch (Exception $e) {
  echo "<script>alert('Error fetching user data: " . $e->getMessage() . "');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Alumni Dashboard | AlumniConnect</title>
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
              <p class="mb-1"><strong>College:</strong> <?= htmlspecialchars($user_data_fetched['alumni_college'] ?? "GEC MODASA") ?></p>
              <p class="mb-1"><strong>Department:</strong> <?= htmlspecialchars($user_data_fetched['branch'] ?? "Not Specified") ?></p>
              <p class="mb-1"><strong>City:</strong> <?= $user_data_fetched['alumni_city'] ? htmlspecialchars($user_data_fetched['alumni_city']) : "<p class='text-danger'>Update City</p>" ?></p>

            </div>
          </div>

          <!-- Right side: Info cards -->
          <!-- Right side: Combined Info Cards (2x2 inside one big card) -->
          <div class="col-lg-7">
            <div class="card p-4 h-100">
              <div class="row g-4">

                <!-- Revenue -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fa-solid fa-building p-2 m-2 text-primary"></i>
                    <div>
                      <h6 class="mb-0">Company</h6>

                      <?php
                      if (!empty($user_data_fetched['alumni_company'])) {
                      ?>
                        <strong><?= htmlspecialchars($user_data_fetched['alumni_company']); ?></strong>
                        <div class="text-muted small">#Your Company</div>
                      <?php } else { ?>
                        <p><strong>#YourCompany </strong></p>
                        <div class="text-muted small">#Your Company</div>
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

                <!-- Batch -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fa-solid fa-arrows-turn-to-dots p-2 m-2 text-primary"></i>
                    <div>
                      <h6 class="mb-0">Batch</h6>
                      <strong><?= htmlspecialchars($user_data_fetched['alumni_batch'] ?? '') ?></strong>
                      <div class="text-muted small">#Your Batch</div>
                    </div>
                  </div>
                </div>

                <!-- Designation -->
                <div class="col-md-6">
                  <div class="p-3 border rounded h-100 d-flex align-items-start">
                    <i class="fa-solid fa-arrows-turn-to-dots p-2 m-2 text-primary"></i>
                    <div>
                      <h6 class="mb-0">Designation</h6>
                      <strong><?= htmlspecialchars($user_data_fetched['alumni_designation'] ?? '') ?></strong>
                      <div class="text-muted small">#Your Designation</div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="button-group-container">
        <button id="displayed_details" class="btn-custom-left">
          <i class="fas fa-info-circle"></i>Social Details
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
                <?php if (!empty($user_data_fetched['alumni_github_link'])): ?>
                  <a href="<?= htmlspecialchars($user_data_fetched['alumni_github_link']) ?>" target="_blank"><?= htmlspecialchars($user_data_fetched['alumni_github_link']) ?></a>
                <?php else: ?>
                  <p class="text-muted">#NoGitHubLink</p>
                <?php endif; ?>
                <p class="text-muted p-2">#Show my projects on</p>
              </div>
            </div>

            <!-- LinkedIn -->
            <div class="col-md-6 mb-3">
              <div class="card p-3 h-100">
                <h6 class="text-info" style="margin-top: 8px;">LinkedIn</h6>
                <?php if (!empty($user_data_fetched['alumni_linkedin_link'])): ?>
                  <a href="<?= htmlspecialchars($user_data_fetched['alumni_linkedin_link']) ?>" target="_blank" style="margin-top: 12px;"><?= htmlspecialchars($user_data_fetched['alumni_linkedin_link']) ?></a>
                <?php else: ?>
                  <p class="text-muted">#NoLinkedInLink</p>
                <?php endif; ?>
                <p class="text-muted p-2">#Show my profile on</p>
              </div>
            </div>
          </div>
        </div>
      </center>
    </div>
  </div>

  <script src="./script.js"></script>
</body>

</html>