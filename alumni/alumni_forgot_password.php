<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';
include "./alumni_favicon.php";

if (!isset($conn)) {
    die("Database connection not established.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['reset_password_btn'])) {
        $alumni_email = trim($_POST['email'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // 1. Basic empty checks
        if (empty($alumni_email) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All fields are required.";
            header("Location: ./alumni_forgot_password.php?error=" . urlencode($error_message));
            exit();
        }

        // 2. Password match check
        if ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match.";
            header("Location: ./alumni_forgot_password.php?error=" . urlencode($error_message));
            exit();
        }

        // 3. Password complexity check (same rules as your JS validation)
        $hasUpperCase = preg_match('/[A-Z]/', $new_password);
        $hasNumber = preg_match('/[0-9]/', $new_password);
        $hasSpecialChar = preg_match('/[^a-zA-Z0-9]/', $new_password);
        $hasMinLength = strlen($new_password) >= 8;

        if (!$hasUpperCase || !$hasNumber || !$hasSpecialChar || !$hasMinLength) {
            $error_message = "Password must be at least 8 characters and include an uppercase letter, a number, and a special symbol.";
            header("Location: ./alumni_forgot_password.php?error=" . urlencode($error_message));
            exit();
        }

        // 4. Check if the alumni email exists and is a registered account
        $check_email = "SELECT alumni_id FROM alumni_student_master WHERE email = ? AND is_registered = 1";
        $check_stmt = $conn->prepare($check_email);
        $check_stmt->bind_param("s", $alumni_email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows === 1) {
            $alumni = $result->fetch_assoc();

            // 5. Hash the new password using Bcrypt
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // 6. Update the password in the database
            $update_query = "UPDATE alumni_student_master SET password_hash = ? WHERE alumni_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $alumni['alumni_id']);

            if ($update_stmt->execute()) {
                $success_message = "Password reset successful. You can now log in with your new password.";
                header("Location: ../login.php?success=" . urlencode($success_message));
                exit();
            } else {
                $error_message = "Something went wrong. Please try again.";
                header("Location: ./alumni_forgot_password.php?error=" . urlencode($error_message));
                exit();
            }
        } else {
            $error_message = "No registered alumni account found with that email.";
            header("Location: ./alumni_forgot_password.php?error=" . urlencode($error_message));
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
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>AlumniConnect | Forgot Password</title>
</head>

<body style="font-family: 'Inter', sans-serif; background-color: #e7e7e7; color: #2b2f31; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">

    <div style="width: 100%; max-width: 448px;">
        <div style="background-color: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #d6e2ef;">
            <p id="message" style="color: <?php echo isset($_GET["error"]) ? '#d92d20' : '#0a7d3e'; ?>; text-align: center; margin-bottom: 16px;">
                <?php echo isset($_GET["error"]) ? htmlspecialchars($_GET["error"]) : (isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : ''); ?>
            </p>
            <!-- script for UI changes -->
            <script>
                const message = document.getElementById("message");
                setTimeout(() => {
                    message.style.display = "none";
                }, 2000)
            </script>
            <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #2E75B6; margin-bottom: 24px;">Forgot Password</h1>
            <p style="text-align: center; color: #667079; margin-bottom: 32px;">Enter your registered email and set a new password.</p>

            <form id="forgotPasswordForm" method="post" action="" style="padding: 0; margin: 0;">
                <div style="margin-bottom: 24px;">
                    <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #667079; margin-bottom: 8px;">Email address</label>
                    <input type="email" id="email" name="email" required style="width: 100%; padding: 12px 16px; background-color: #ffffff; border: 1px solid #d6e2ef; color: #2b2f31; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="new_password" style="display: block; font-size: 14px; font-weight: 500; color: #667079; margin-bottom: 8px;">New Password</label>
                    <input type="password" id="new_password" name="new_password" required style="width: 100%; padding: 12px 16px; background-color: #ffffff; border: 1px solid #d6e2ef; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: #2b2f31; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="confirm_password" style="display: block; font-size: 14px; font-weight: 500; color: #667079; margin-bottom: 8px;">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 12px 16px; background-color: #ffffff; border: 1px solid #d6e2ef; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: #2b2f31; box-sizing: border-box;">
                </div>

                <button type="submit" name="reset_password_btn" id="reset_password_btn" style="width: 100%; background-color: #2E75B6; color: #FFFFFF; font-weight: 700; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out; box-sizing: border-box;">
                    Reset Password
                </button>

            </form>

            <p style="text-align: center; margin-top: 20px; font-size: 14px;">
                <a href="../login.php" style="color: #2E75B6; text-decoration: none; font-weight: 600;">&larr; Back to Login</a>
            </p>

            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
        </div>

        <div style="margin-top: 32px; text-align: center; color: #667079; font-size: 14px;">
            &copy; 2024 AlumniConnect. All rights reserved.
        </div>
    </div>
</body>
<script src="../scripts/script.js"></script>

</html>