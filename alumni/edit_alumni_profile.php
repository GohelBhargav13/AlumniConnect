<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['Enroll_no_alumni'])) {
    header('Location: ../login.php');
    exit();
}
$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no_alumni'];

try {
    if (!isset($_GET['edit'])) {
        $_SESSION['message'] = ['success' => false, 'err_message' => 'Alumni not found'];
        exit();
    }

    $alumni_id_edit = (int) $_GET['edit'];
    $fetch_user_data = "SELECT * FROM alumnimaster WHERE alumni_id = ?";
    $fetch_user_stmt = $conn->prepare($fetch_user_data);
    $fetch_user_stmt->bind_param('i', $alumni_id_edit);
    $fetch_user_stmt->execute();
    $user_data = $fetch_user_stmt->get_result();
    if ($user_data->num_rows === 1) {
        $user_data_fetched = $user_data->fetch_assoc();
    }
    $fetch_user_stmt->close();
} catch (Exception $th) {
    echo "<script>alert('Data not found of the user: " . htmlspecialchars($th->getMessage()) . "')</script>"; // Use getMessage()
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile_btn'])) {

    $alumni_name = $_POST['name'] ?? '';
    $alumni_email = $_POST['email'] ?? '';
    $alumni_phoneNo = $_POST['phone_no'] ?? 0;
    $alumni_dep = $_POST['department'] ?? '';
    $alumni_add_year = $_POST['admission_year'] ?? 0;
    $alumni_pass_year = $_POST['passout_year'] ?? 0;
    $alumni_college = $_POST['college'] ?? '';
    $alumni_city = $_POST['city'] ?? '';
    $alumni_github_link = $_POST['github'] ?? '';
    $alumni_linkedIn_link = $_POST['linkedin'] ?? '';
    $alumni_bio = $_POST['bio'] ?? '';

    if (!empty($alumni_name) || !empty($alumni_email) || !empty($alumni_phoneNo) || !empty($alumni_dep) || !empty($alumni_add_year) || !empty($alumni_pass_year) || !empty($alumni_college) || !empty($alumni_city)) { // Use !empty for better validation
        try {
            $sql_for_update = "UPDATE alumnimaster SET alumni_name=?, alumni_email=?, alumni_phone_no=?, alumni_add_year=?, alumni_pass_year=?, alumni_bio=?, alumni_githublink=?, alumni_linkedIn=?, alumni_city=?, alumni_department=?, alumni_college=? WHERE alumni_id=?";
            $sql_for_stmt = $conn->prepare($sql_for_update);
            $sql_for_stmt->bind_param(
                "ssiiissssssi",
                $alumni_name,
                $alumni_email,
                $alumni_phoneNo,
                $alumni_add_year,
                $alumni_pass_year,
                $alumni_bio,
                $alumni_github_link,
                $alumni_linkedIn_link,
                $alumni_city,
                $alumni_dep,
                $alumni_college,
                $alumni_id_edit
            );
            $sql_for_stmt->execute();

            if ($sql_for_stmt->affected_rows === 1) {
                $_SESSION['message'] = ["success" => true, "mess" => htmlspecialchars($alumni_name) . " Details Updated"];
            } else {
                $_SESSION['message'] = ["success" => false, "mess" => "Error in updating data or no changes made."]; // More descriptive message
            }
            // Redirect after successful update or no changes to prevent form resubmission
            header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit);
            exit();
        } catch (Exception $th) {
            $_SESSION['message'] = ["success" => false, "mess" => "Database error: " . htmlspecialchars($th->getMessage())];
            header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit);
            exit();
        }
    } else {
        $_SESSION['message'] = ["success" => false, "mess" => "Please fill all required fields"];
        header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit); // Redirect back to the form with message
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile | AlumniConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/edit_alumni_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>AlumniConnect</h2>
            </div>
            <hr style="border: 1px solid gray; width:100%;" class="p-2 m-2 text-muted">
            <ul class="sidebar-menu">
                <li><a href="./alumni_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="./alumni_create_post.php"><i class="fas fa-briefcase"></i>Create Post</a></li>
                <li><a href="#"><i class="fas fa-newspaper"></i>Articles</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Collections</a></li>
                <li><a href="./edit_alumni_profile.php?edit=<?= $_SESSION['alumni_id'] ?>" class="active"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="content-area">
            <h1 class="dashboard-title">Edit Your Profile</h1>
            <div class="profile-card">
                <form class="profile-form" action="edit_alumni_profile.php?edit=<?= htmlspecialchars($alumni_id_edit) ?>" method="POST">
                    <?php if (isset($_SESSION['message'])): ?>
                        <?php
                        $messageClass = $_SESSION['message']['success'] ? 'success' : 'error';
                        $messageText = $_SESSION['message']['mess'] ?? "";
                        ?>
                        <p id="message" class="<?= htmlspecialchars($messageClass) ?>" style="
                            color: white;
                            border: 1px solid;
                            background-color: <?= $_SESSION['message']['success'] ? '#28a745' : '#dc3545' ?>;
                            padding: 8px 10px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            margin-bottom: 10px;
                            display: block;
                            transition: all 0.3s ease-in-out;
                            text-align: center;
                        ">
                            <?= htmlspecialchars($messageText) ?>
                        </p>

                        <script>
                            const messageElement = document.getElementById('message');
                            setTimeout(() => {
                                if (messageElement) {
                                    messageElement.style.display = 'none';
                                }
                            }, 2 * 1000);
                        </script>
                    <?php endif; ?>
                    <?php unset($_SESSION['message']); // Clear message after display ?>

                    <div class="form-group">
                        <label for="Enrollment">Enrollment No</label>
                        <input type="text" id="Enrollment" name="Enrollment" value="<?= htmlspecialchars($user_data_fetched['Enrollment_No'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data_fetched['alumni_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data_fetched['alumni_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone_no">Phone Number</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($user_data_fetched['alumni_phone_no'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select name="department" id="department">
                            <option value="Computer Engineering" <?= (isset($user_data_fetched['alumni_department']) && $user_data_fetched['alumni_department'] == "Computer Engineering") ? "selected" : "" ?>>Computer Engineering</option>
                            <option value="Information Technology" <?= (isset($user_data_fetched['alumni_department']) && $user_data_fetched['alumni_department'] == "Information Technology") ? "selected" : "" ?>>Information Technology</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admission_year">Admission Year</label>
                        <input type="number" id="admission_year" name="admission_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['alumni_add_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="passout_year">Passout Year</label>
                        <input type="number" id="passout_year" name="passout_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['alumni_pass_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="college">College Name</label>
                        <select name="college" id="college">
                            <option value="GEC MODASA" <?= (isset($user_data_fetched['alumni_college']) && $user_data_fetched['alumni_college'] == "GEC MODASA") ? "selected" : "" ?>>GEC MODASA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user_data_fetched['alumni_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="github">GitHub Link</label>
                        <input type="url" id="github" name="github" value="<?= htmlspecialchars($user_data_fetched['alumni_githublink'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin">LinkedIn Link</label>
                        <input type="url" id="linkedin" name="linkedin" value="<?= htmlspecialchars($user_data_fetched['alumni_linkedIn'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Write a short bio..."><?= htmlspecialchars($user_data_fetched['alumni_bio'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit" name="save_profile_btn">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>