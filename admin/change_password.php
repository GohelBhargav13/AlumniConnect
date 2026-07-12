<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../utills/db_conn.php";
include("./admin_favicon.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}
if (!isset($conn)) {
    die("database connection is not established");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['change_btn'])) {
        $admin_new_password = $_POST['password'];
        $admin_confirm_password = $_POST['confirm_password'];

        if (empty($admin_new_password) || empty($admin_confirm_password)) {
            $error_message = "Both password fields are required.";
            header("Location: ./change_password.php?error=" . urlencode($error_message));
            exit();
        }

        if (strlen($admin_new_password) < 8 or strlen($admin_confirm_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
            header("Location: ./change_password.php?error=" . urlencode($error_message));
            exit();
        }

        if (strlen($admin_new_password) != strlen($admin_confirm_password)) {
            $error_message = "Password lengths do not match.";
            header("Location: ./change_password.php?error=" . urlencode($error_message));
            exit();
        }

        if ($admin_new_password !== $admin_confirm_password) {
            $error_message = "Passwords do not match.";
            header("Location: ./change_password.php?error=" . urlencode($error_message));
            exit();
        }

        //convert the admin password into the hash
        $admin_hashed_pass = password_hash($admin_new_password, PASSWORD_DEFAULT);
        $update_admin_sql = "
            UPDATE adminmaster 
            SET password = ? 
            WHERE admin_id = ? 
        ";
        $update_stmt = $conn->prepare($update_admin_sql);
        $update_stmt->bind_param("si", $admin_hashed_pass, $_SESSION["admin_id"]);

        if ($update_stmt->execute()) {
            $message = "Password updated successfully";
            header("Location: ./change_password.php?success=" . urlencode($message));
            exit();
        } else {
            $error_message = "Error updating password: " . $conn->error;
            header("Location: ./change_password.php?error=" . urlencode($error_message));
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
    <title>AlumniConnect | Admin Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #2b2f31;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            border: 1px solid #d6e2ef;
            border-radius: 10px;
            margin: 20px;
            overflow: hidden;
        }

        .admin-main {
            flex-grow: 1;
            padding: 20px;
            box-sizing: border-box;
            background-color: #ffffff;
        }

        .admin-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 40px;
            border-bottom: 1px solid #d6e2ef;
            padding-bottom: 20px;
            color: #2E75B6;
        }

        .form-wrapper {
            width: 100%;
            max-width: 448px;
            margin: 0 auto;
        }

        .form-card {
            background-color: #f4f8fc;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #d6e2ef;
            box-sizing: border-box;
        }

        .form-card h1 {
            font-size: 30px;
            font-weight: 600;
            text-align: center;
            color: #2E75B6;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #667079;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background-color: #ffffff;
            border: 1px solid #d6e2ef;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease-in-out;
            color: #2b2f31;
            box-sizing: border-box;
        }

        .submit-btn {
            width: 100%;
            background-color: #2E75B6;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .submit-btn:hover {
            background-color: #1F5A94;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 600px) {
            .admin-wrapper {
                margin: 8px;
                flex-direction: column;
            }

            .admin-title {
                font-size: 20px;
            }

            .form-card {
                padding: 22px;
            }

            .form-wrapper {
                margin: 0 8px;
            }
        }
    </style>
</head>

<body>

    <!-- Main container for the entire admin panel -->
    <div class="admin-wrapper">

        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div class="admin-main">
            <h1 class="admin-title">Admin Panel</h1>
            <center>
                <div class="form-wrapper">
                    <p id="message" style="color: <?php echo isset($_GET["success"]) ? '#0a7d3e' : '#d92d20'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <div class="form-card">
                        <h1>Admin Change Password</h1>

                        <form id="loginForm" method="post" accept="#">

                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" id="password" name="password" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_btn" class="submit-btn">
                                change password
                            </button>
                        </form>

                        <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #d92d20; display: none;"></div>
                    </div>
                </div>
            </center>
        </div>
    </div>

</body>

</html>