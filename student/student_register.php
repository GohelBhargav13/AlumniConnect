<?php
require_once '../utills/db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_click_btn'])) {
    $student_enrollment = (int) $_POST['enrollmentNo'] ?? 0;
    $student_name = $_POST['name'] ?? '';
    $student_email =  filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $student_phoneNo = (int) $_POST['phoneNo'] ?? 0;
    $student_admissionYear = (int) $_POST['admissionYear'] ?? 0;
    $student_passoutYear = (int) $_POST['passOutYear'] ?? 0;
    $student_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';

    try {
        $exist_user = "SELECT * FROM studentmaster WHERE Enrollment_no = ?";
        $exist_user_stmt = $conn->prepare($exist_user);
        $exist_user_stmt->bind_param('i', $student_enrollment);
        $exist_user_stmt->execute();
        $exist_user_res = $exist_user_stmt->get_result();

        if ($exist_user_res->num_rows > 0) {
            $_SESSION['message'] = ['sucess' => true, 'mess' => 'This Enrollment is already Exists'];
            header('Location: student_register.php');
            exit();
        }
        
        if ($student_password !== $confirm_password) {
            $_SESSION['message'] = ['success' => false, 'mess' => 'Passwords do not match'];
            header('Location: student_register.php');
            exit();
        }

        $hashed_password = password_hash($student_password, PASSWORD_DEFAULT);
        $register_new_student = "INSERT INTO studentmaster (Enrollment_no,student_name,student_email,student_phone_no,student_add_year,student_pass_year,student_password) VALUES (?,?,?,?,?,?,?)";
        $register_new_stmt = $conn->prepare($register_new_student);
        $register_new_stmt->bind_param('issiiis', $student_enrollment, $student_name, $student_email, $student_phoneNo, $student_admissionYear, $student_passoutYear, $hashed_password);
    
        if ($register_new_stmt->execute()) {
            $_SESSION['message'] = ['sucess' => true, 'mess' => 'Registration Sucessfully'];
            $student_id = $register_new_stmt->insert_id;
            $_SESSION['student_get_id'] = $student_id;
        } else {
            $_SESSION['message'] = ['sucess' => false, 'mess' => 'Registartion Error'];
        }

        $exist_user_stmt->close();
        $register_new_stmt->close();
        header("Location:student_register.php");
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
    <title>Student Register | AlumniConnect</title>
    <link rel="stylesheet" href="../style/register.css">
</head>

<body>
    <div class="container">
        <div class="illustration-section">
            <img src="path/to/your/illustration.svg" alt="Illustration of person working">
        </div>
        <div class="form-section">
            <div class="registration-card">
                <div class="logo">
                    <img src="path/to/your/unicorn-icon.png" alt="UI Unicorn Logo">
                    <span>UI Unicorn</span>
                </div>
                <h2>Join UI Unicorn</h2>
                <?php if (isset($_SESSION['message'])): ?>
                    <p id="message" class="msg" style="text-align: center; margin-top: 10px;"><?= htmlspecialchars($_SESSION['message']['mess']) ?></p>
                    <script>
                        const mess = document.getElementById('message');
                        setTimeout(() => {
                            mess.style.display = 'none'
                        },2 * 1000)
                        
                    </script>
                <?php unset($_SESSION['message']); ?>
                <?php endif ?>
                <form action="student_register.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="enrollmentNo">Enrollment No.</label>
                        <input type="number" id="enrollmentNo" name="enrollmentNo" placeholder="Enter enrollment number" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label for="phoneNo">Phone Number</label>
                        <input type="number" id="phoneNo" name="phoneNo" placeholder="Enter your phone number" required>
                    </div>

                    <div class="form-group half-width-group">
                        <div class="half-width-field">
                            <label for="admissionYear">Admission Year</label>
                            <input type="number" id="admissionYear" name="admissionYear" placeholder="e.g., 2020" min="1900" max="2025" required>
                        </div>
                        <div class="half-width-field">
                            <label for="passOutYear">Pass Out Year</label>
                            <input type="number" id="passOutYear" name="passOutYear" placeholder="e.g., 2024" min="1900" max="2099">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="btn-primary" name="register_click_btn">Register Now</button>
                </form>

                <div class="login-link">
                    <span>Already have an account? <a href="../login.php">Sign in here</a></span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>