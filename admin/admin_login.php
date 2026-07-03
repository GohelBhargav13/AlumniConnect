<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../utills/db_conn.php';

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
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>AlumniConnect Admin Login</title>
</head>

<body style="font-family: 'Inter', sans-serif; background-color: #12151D; color: #E2E8F0; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">

    <div style="width: 100%; max-width: 448px;">
        <div style="background-color: #1D2129; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #4B5563;">
            <p id="message" style="color: <?php echo isset($_GET["error"]) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 16px;">
                <?php echo isset($_GET["error"]) ? htmlspecialchars($_GET["error"]) : ''; ?>
            </p>
            <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #FFFFFF; margin-bottom: 24px;">Admin Login</h1>
            <p style="text-align: center; color: #9CA3AF; margin-bottom: 32px;">Sign in to manage the AlumniConnect platform.</p>

            <form id="loginForm" method="post" accept="#" style="padding: 10px;margin-right: 10px;">
                <div style="margin-bottom: 24px;">
                    <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #9CA3AF; margin-bottom: 8px;">Email address</label>
                    <input type="email" id="email" onmouseover="changeStyleInput('email')" onmouseleave="resetStyleInput('email')" name="email" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; color: white; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="password" style="display: block; font-size: 14px; font-weight: 500; color: #9CA3AF; margin-bottom: 8px;">Password</label>
                    <input type="password" id="password" onmouseover="changeStyleInput('password')" onmouseleave="resetStyleInput('password')" name="password" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white;">
                </div>

                <button type="submit" name="login_btn" id="login_btn"  onmousemove="changeStyleBtn('login_btn')" style="width: 100%; margin-left: 15px; background-color: #3B82F6; color: #FFFFFF; font-weight: 700; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out;">
                    Login
                </button>
            </form>

            <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #F87171; display: none;"></div>
        </div>

        <div style="margin-top: 32px; text-align: center; color: #6B7280; font-size: 14px;">
            &copy; 2024 AlumniConnect. All rights reserved.
        </div>
    </div>
</body>
    <script src="../scripts/script.js"></script>
</html>