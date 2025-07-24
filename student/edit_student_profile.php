<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['Enroll_no'])) {
    header('Location: ../login.php');
    exit();
}
$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no'];

try {
    if (!isset($_GET['edit'])) {
        $_SESSION['message'] = ['success' => false, 'err_message' => 'Student not found'];
        exit();
    }
    $student_id_edit = (int) $_GET['edit'];
    $fetch_user_data = "SELECT * FROM studentmaster WHERE Enrollment_no = ?";
    $fetch_user_stmt = $conn->prepare($fetch_user_data);
    $fetch_user_stmt->bind_param('i', $loggedIn_user);
    $fetch_user_stmt->execute();
    $user_data = $fetch_user_stmt->get_result();
    if ($user_data->num_rows === 1) {
        $user_data_fetched = $user_data->fetch_assoc();
    }
    $fetch_user_stmt->close();
} catch (Exception $th) {
    echo "<script>alert('Data not found of the user: $th')</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile_btn'])) {

    $student_name = $_POST['name'] ?? '';
    $student_email = $_POST['email'] ?? '';
    $student_phoneNo = $_POST['phone_no'] ?? 0;
    $student_dep = $_POST['department'] ?? '';
    $student_add_year = $_POST['admission_year'] ?? 0;
    $student_pass_year = $_POST['passout_year'] ?? 0;
    $student_college = $_POST['college'] ?? '';
    $student_city = $_POST['city'] ?? '';
    $student_github_link = $_POST['github'] ?? '';
    $student_linkedIn_link = $_POST['linkedin'] ?? '';
    $student_bio = $_POST['bio'] ?? '';

    if (isset($student_name) || isset($student_email) || isset($student_phoneNo) || isset($student_dep) || isset($student_add_year) || isset($student_pass_year) || isset($student_college) || isset($student_city)) {
        try {
            $sql_for_update = "UPDATE studentmaster SET student_name=?, student_email=?, student_phone_no=?, student_add_year=?, student_pass_year=?, student_bio=?, student_github=?, student_linkedIn=?, student_city=?, student_department=?, student_college=? WHERE Enrollment_no =?";
            $sql_for_stmt = $conn->prepare($sql_for_update);
            $sql_for_stmt->bind_param(
                "ssiiissssssi",
                $student_name,
                $student_email,
                $student_phoneNo,
                $student_add_year,
                $student_pass_year,
                $student_bio,
                $student_github_link,
                $student_linkedIn_link,
                $student_city,
                $student_dep,
                $student_college,
                $loggedIn_user
            );
            $sql_for_stmt->execute();

            if ($sql_for_stmt->affected_rows === 1) {
                $_SESSION['message'] = ["success" => true, "mess" => "$student_name Details Updated"];
                // exit();
            } else {
                $_SESSION['message'] = ["success" => false, "mess" => "Error in updating data"];
                header("Location: edit_student_profile.php?edit=$loggedIn_user");
            }
        } catch (Exception $th) {
            $_SESSION['message'] = ["success" => false, "mess" => $th];
        }
    } else {
        $_SESSION['message'] = ["success" => false, "mess" => "Please fill all required fields"];
        header("Location: edit_student_profile.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile | AlumniConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/edit_student_profile.css">
</head>

<body>
    <div class="dashboard">
      <?php include './sidebar.php' ?>
        <main class="main-container">
            <h1 class="dashboard-title">Edit Your Profile</h1>
            <div class="profile-card">
                <form class="profile-form" action="edit_student_profile.php?edit=<?= $loggedIn_user?>" method="POST">
                    <?php if (isset($_SESSION['message'])): ?>
                        <?php
                        if ($_SESSION['message']['success'] == true){ ?>
                            <p id="message" style="color : white;
                                border : 1px solid green;
                                background-color : lightgreen;
                                padding : 8px 10px;
                                border-radius : 6px;
                                font-size : 14px;
                                font-weight :500;
                                margin-bottom : 10px;
                                display : block;
                                transition : all 0.3s ease-in-out;"> <?php echo $_SESSION['message']['mess'] ?? "" ?></p>
  
                        <?php } else {?>
                            <p id="message" style="color : white;
                                border : 1px solid red;
                                background-color : red;
                                padding : 8px 10px;
                                border-radius : 6px;
                                font-size : 14px;
                                font-weight :500;
                                margin-bottom : 10px;
                                display : block;
                                transition : all 0.3s ease-in-out;"> <?php echo $_SESSION['message']['mess'] ?? "" ?></p>
                        <?php } ?>
                        <script>
                            const message = document.getElementById('message');
                            setTimeout(() => {
                                message.style.display = 'none';
                            }, 2 * 1000);
                        </script>
                    <?php endif; ?>
                    <?php unset($_SESSION['message']); ?>
                    <div class="form-group">
                        <label for="Enrollment">Enrollment No</label>
                        <input type="text" id="Enrollment" name="Enrollment" value="<?= htmlspecialchars($user_data_fetched['Enrollment_no'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data_fetched['student_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data_fetched['student_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone_no">Phone Number</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($user_data_fetched['student_phone_no'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select name="department" id="department">
                            <option value="Computer Engineering" <?= (isset($user_data_fetched['student_department']) && $user_data_fetched['student_department'] == "Computer Engineering") ? "selected" : "" ?>>Computer Engineering</option>
                            <option value="Information Technology" <?= (isset($user_data_fetched['student_department']) && $user_data_fetched['student_department'] == "Information Technology") ? "selected" : "" ?>>Information Technology</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admission_year">Admission Year</label>
                        <input type="number" id="admission_year" name="admission_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['student_add_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="passout_year">Passout Year</label>
                        <input type="number" id="passout_year" name="passout_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['student_pass_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="college">College Name</label>
                        <select name="college" id="college">
                            <option value="GEC MODASA" <?= (isset($user_data_fetched['college']) && $user_data_fetched['college'] == "GEC MODASA") ? "selected" : "" ?>>GEC MODASA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user_data_fetched['student_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="github">GitHub Link</label>
                        <input type="url" id="github" name="github" value="<?= htmlspecialchars($user_data_fetched['student_github'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin">LinkedIn Link</label>
                        <input type="url" id="linkedin" name="linkedin" value="<?= htmlspecialchars($user_data_fetched['student_linkedIn'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Write a short bio..."><?= htmlspecialchars($user_data_fetched['student_bio'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit" name="save_profile_btn">Save Profile</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>