<?php
if (session_status() === PHP_SESSION_NONE) session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include "../utills/db_conn.php";
require '../vendor/autoload.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}
if (!isset($conn)) {
    die("database connection is not established");
}

$total_analystics = "SELECT 
                        (SELECT COUNT(*) FROM alumnimaster) AS alumni_count,
                        (SELECT COUNT(*) FROM postmaster) AS post_count,
                        (SELECT COUNT(*) FROM studentmaster WHERE req_status = 'accepted') AS student_count;";

$total_analystics_res = $conn->query($total_analystics);
if ($total_analystics_res) {
    $final_analystics2 =  $total_analystics_res->fetch_assoc();
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
        $admin_hashed_pass = password_hash($admin_new_password,PASSWORD_DEFAULT);
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
            }else {
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
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire admin panel -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Admin Panel</h1>
            <center>
                <div style="width: 100%; max-width: 448px; margin: 0 16px;">
                        <p id="message" style="color: <?php echo isset($_GET["success"]) ? 'green' : 'red'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>

                    <div style="background-color: #1D2129; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #4B5563;">
                        <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #FFFFFF; margin-bottom: 24px;">Admin Change Password</h1>

                        <form id="loginForm" method="post" accept="#">
                
                            <div style="margin-bottom: 24px;">
                                <label for="text" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">New Password</label>
                                <input type="password" id="password" name="password" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white;">
                            </div>

                             <div style="margin-bottom: 24px;">
                                <label for="text" style="display: block; font-size: 14px; font-weight: 500; color: #fcfcfc; margin-bottom: 8px;">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: white;">
                            </div>

                            <button type="submit" name="change_btn" style="width: 100%; background-color: #3B82F6; color: #FFFFFF; font-weight: 700; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; transition: background-color 0.2s ease-in-out;">
                                change password
                            </button>
                        </form>

                        <div id="messageBox" style="margin-top: 24px; text-align: center; font-size: 14px; font-weight: 500; color: #F87171; display: none;"></div>
                    </div>
                </div>
            </center>
        </div>
    </div>

</body>

</html>