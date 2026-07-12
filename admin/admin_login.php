<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';
include("./admin_favicon.php");

if (!isset($conn)) {
    die("Database connection not established.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['login_btn'])) {
        $admin_email = $_POST['email'] ?? '';
        $admin_password = $_POST['password'] ?? '';

        if (empty($admin_email) || empty($admin_password)) {
            $_SESSION['message'] = ["sucess" => false, "error_msg" => "Email and Password are required."];
            header("Location: ./admin_login.php");
            exit();
        }

        if (strlen($admin_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
            header("Location: ./admin_login.php?error=" . urlencode($error_message));
            exit();
        }

        $exist_user = "SELECT * FROM adminmaster WHERE admin_email = ? ";
        $exist_user_stmt = $conn->prepare($exist_user);
        $exist_user_stmt->bind_param("s", $admin_email);

        $exist_user_stmt->execute();
        $result = $exist_user_stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($admin_password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_name'];

                header("Location: ./admin_dashboard.php");
                exit;
            } else {
                $errror_message = "Invalid password.";
                header("Location: ./admin_login.php?error=" . urlencode($errror_message));
                exit();
            }
        } else {
            $errror_message = "Admin not found with the provided email.";
            header("Location: ./admin_login.php?error=" . urlencode($errror_message));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/gec_favicon.png">
    <title>Alumni Portal | GEC Modasa</title>
</head>

<body style="font-family: 'Inter', sans-serif; background-color: #e7e7e7; color: #2b2f31; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">

    <div style="width: 100%; max-width: 448px;">
        <div style="background-color: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #d6e2ef;">
           <p id="message" style="color: <?php echo isset($_GET["error"]) ? '#d92d20' : '#0a7d3e'; ?>; text-align: center; margin-bottom: 16px;">
                <?php echo isset($_GET["error"]) ? htmlspecialchars($_GET["error"]) : (isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : ''); ?>
            </p>
            <!-- Script for message remove automatically -->
            <script>
                const message = document.getElementById("message");
                setTimeout(() => {
                    message.style.display = "none";
                }, 2000)
            </script>
            <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #2E75B6; margin-bottom: 24px;">Admin Login</h1>
            <p style="text-align: center; color: #667079; margin-bottom: 32px;">Sign in to manage the AlumniConnect platform.</p>

            <form id="loginForm" method="post" accept="#" style="padding: 0; margin: 0;">
                <div style="margin-bottom: 24px;">
                    <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #667079; margin-bottom: 8px;">Email address</label>
                    <input type="email" id="email" onmouseover="changeStyleInput('email')" onmouseleave="resetStyleInput('email')" name="email" required style="width: 100%; padding: 12px 16px; background-color: #ffffff; border: 1px solid #d6e2ef; color: #2b2f31; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="password" style="display: block; font-size: 14px; font-weight: 500; color: #667079; margin-bottom: 8px;">Password</label>
                    <input type="password" minlength="8" id="password" onmouseover="changeStyleInput('password')" onmouseleave="resetStyleInput('password')" name="password" required style="width: 100%; padding: 12px 10px; background-color: #ffffff; border: 1px solid #d6e2ef; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: #2b2f31; box-sizing: border-box;">
                </div>
                <a href="./admin_forgot_password.php" style="display: flex; justify-content: end; margin-bottom: 8px; text-decoration:none;">Forgot password ?</a>

                <button type="submit" name="login_btn" id="login_btn" onmousemove="changeStyleBtn('login_btn')" style="width: 100%; background-color: #2E75B6; color: #FFFFFF; font-weight: 700; padding: 12px 12px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out; box-sizing: border-box;">
                    Login
                </button>
                <p style="text-align: center;">Do you want to <a href="../index.php">Go Back</a>?</p>
            </form>

            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
        </div>

        <div style="margin-top: 32px; text-align: center; color: #667079; font-size: 14px;">
            &copy; 2024 AlumniConnect. All rights reserved.
        </div>
    </div>
</body>
<script src="../scripts/script.js"></script>

</html>