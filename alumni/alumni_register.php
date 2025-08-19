<?php
require_once '../utills/db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_click_btn'])) {
    $alumni_enrollment = (int) $_POST['enrollmentNo'] ?? 0;
    $alumni_name = $_POST['name'] ?? '';
    $alumni_email =  filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $alumni_phoneNo = (int) $_POST['phoneNo'] ?? 0;
    $alumni_admissionYear = (int) $_POST['admissionYear'] ?? 0;
    $alumni_passoutYear = (int) $_POST['passOutYear'] ?? 0;
    $alumni_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';
    $company_name = $_POST['company'] ?? '';
    $idCardPath = null;

    try {
        // check if enrollment already exists
        $exist_user = "SELECT * FROM alumnimaster WHERE Enrollment_no = ?";
        $exist_user_stmt = $conn->prepare($exist_user);
        $exist_user_stmt->bind_param('i', $alumni_enrollment);
        $exist_user_stmt->execute();
        $exist_user_res = $exist_user_stmt->get_result();

        if ($exist_user_res->num_rows > 0) {
            $_SESSION['message'] = ['sucess' => false, 'mess' => 'This Enrollment is already Exists'];
            header('Location: alumni_register.php');
            exit();
        }

        // password check
        if ($alumni_password !== $confirm_password) {
            $_SESSION['message'] = ['success' => false, 'mess' => 'Passwords do not match'];
            header('Location: alumni_register.php');
            exit();
        }

        // handle file upload (ID Card)
        if (isset($_FILES['idCard']) && $_FILES['idCard']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/idcards/alumni/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileTmpPath = $_FILES['idCard']['tmp_name'];
            $fileExt = pathinfo($_FILES['idCard']['name'], PATHINFO_EXTENSION);
            $fileName = $alumni_enrollment . "_idcard." . $fileExt;
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $idCardPath = $fileName;
            } else {
                $_SESSION['message'] = ['success' => false, 'mess' => 'Failed to upload ID Card'];
                header('Location: alumni_register.php');
                exit();
            }
        }

        // hash password
        $hashed_password = password_hash($alumni_password, PASSWORD_DEFAULT);

        // insert into db with ID_Card column
        $register_new_alumni = "INSERT INTO alumnimaster 
            (Enrollment_no, alumni_name, alumni_email, alumni_phone_no, alumni_add_year, alumni_pass_year, alumni_password, alumni_company_name, ID_Card) 
            VALUES (?,?,?,?,?,?,?,?,?)";
        $register_new_stmt = $conn->prepare($register_new_alumni);
        $register_new_stmt->bind_param('issiiisss', 
            $alumni_enrollment, 
            $alumni_name, 
            $alumni_email, 
            $alumni_phoneNo, 
            $alumni_admissionYear, 
            $alumni_passoutYear, 
            $hashed_password, 
            $company_name,
            $idCardPath
        );

        if ($register_new_stmt->execute()) {
            $_SESSION['message'] = ['sucess' => true, 'mess' => 'Registration Successfully'];
        } else {
            $_SESSION['message'] = ['sucess' => false, 'mess' => 'Registration Error'];
        }

        $exist_user_stmt->close();
        $register_new_stmt->close();
        header("Location: alumni_register.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = ['sucess' => false, 'mess' => 'There is some issue in registration'];
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Register | AlumniConnect</title>
    <link rel="stylesheet" href="../style/alumni_register.css">

</head>

<body>
    <div class="container">
        <div class="logo"> Alumni </div>
        <h2>Alumni Registration</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <p id="message" class="msg" style="text-align: center;"><?= htmlspecialchars($_SESSION['message']['mess']) ?></p>
            <script>
                const mess = document.getElementById('message');
                setTimeout(() => {
                    mess.style.display = 'none'
                }, 2000)
            </script>
            <?php unset($_SESSION['message']); ?>
        <?php endif ?>

        <form action="alumni_register.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="enrollmentNo">Enrollment No.</label>
                <input type="number" name="enrollmentNo" id="enrollmentNo" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="phoneNo">Phone Number</label>
                <input type="number" name="phoneNo" id="phoneNo" required>
            </div>

            <div class="half-width-group">
                <div class="form-group half-width-field">
                    <label for="admissionYear">Admission Year</label>
                    <input type="number" name="admissionYear" id="admissionYear" required>
                </div>
                <div class="form-group half-width-field">
                    <label for="passOutYear">Pass Out Year</label>
                    <input type="number" name="passOutYear" id="passOutYear">
                </div>
            </div>
            <!-- New ID Card Upload -->
            <div class="form-group">
                <label for="idCard">Upload ID Card</label>
                <input type="file" name="idCard" id="idCard" accept="image/*,application/pdf" required>
            </div>
            <div class="form-group">
                <label for="company">Company Name</label>
                <input type="text" name="company" id="company">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required>
            </div>

            <button class="btn-primary" type="submit" name="register_click_btn">Register Now</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="../login.php">Sign in here</a>
        </div>
    </div>

</body>

</html>