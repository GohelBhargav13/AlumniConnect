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
        $admin_email = $_POST['email'];

        $admin_data = [];
        //admin details fetched
        $fetch_admin_details =  $conn->prepare("SELECT admin_name,password FROM adminmaster WHERE admin_id = ?");
        $fetch_admin_details->bind_param("i", $_SESSION['admin_id']);

        if ($fetch_admin_details->execute()) {
            $result = $fetch_admin_details->get_result();
            if($result->num_rows === 1){
            while ($row = $result->fetch_assoc()) {
                $admin_data = $row;
            }
        }
        }

        if ($result->num_rows === 1) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'gohelbhargav401@gmail.com';
                $mail->Password = 'aqknaoglmxclkvct'; // ⚠️ App password - secure in .env if possible
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom($admin_email, 'AlumniConnect');
                $mail->addAddress($admin_email);

                $mail->isHTML(true);
                $mail->Subject = "Changed Password {$admin_data['admin_name']}";
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; background-color:#f9f9f9; padding:20px; color:#333; border:1px solid #ddd; border-radius:8px; max-width:600px; margin:auto;'>

                        <p style='font-size:18px; color:#2c3e50; margin:0 0 10px;'>Hello <strong>{$admin_data['admin_name']}</strong>,</p>

                        <p style='font-size:16px; color:#16a085; font-weight:bold; margin:0 0 20px;'>🔑 Your Admin Account Credentials</p>

                        <p style='margin:8px 0; font-size:15px;'><strong style='color:#2980b9;'>👤 Admin Name:</strong> {$admin_data['admin_name']}</p>
                        <p style='margin:8px 0; font-size:15px;'><strong style='color:#2980b9;'>🔒 Original Password:</strong> {$admin_data['password']}</p>

                        <div style='text-align:center; margin:25px 0;'>
                            <a href='http://localhost/SE_Project/AlumniConnect/admin/admin_login.php' target='_blank'
                            style='background-color:#16a085; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-size:15px; font-weight:bold; display:inline-block;'>
                            Login to Admin Panel
                            </a>
                        </div>

                        <hr style='margin:20px 0; border:none; border-top:1px solid #ddd;'>

                        <p style='font-size:13px; color:#7f8c8d; margin:0;'>This is an automated email from AlumniConnect platform. Please keep your login credentials safe.</p>

                        <p style='font-size:13px; margin-top:10px; color:#2c3e50;'>
                            Regards,<br>
                            <strong>AlumniConnect Team</strong>
                        </p>

                        </div>
                ";


                $mail->send();
            } catch (Exception $e) {
                error_log("Email failed for {$student['student_email']}: " . $mail->ErrorInfo);
            }
        }
    } else {
        $_SESSION['message'] = ["success" => false, "final_msg" => "Invalid Email"];
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

                    <div style="background-color: #1D2129; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #4B5563;">
                        <h1 style="font-size: 30px; font-weight: 600; text-align: center; color: #FFFFFF; margin-bottom: 24px;">Admin Change Password</h1>

                        <form id="loginForm" method="post" accept="#">
                            <div style="margin-bottom: 24px;">
                                <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #9CA3AF; margin-bottom: 8px;">Email address</label>
                                <input type="email" id="email" name="email" required style="width: 100%; padding: 12px 16px; background-color: #242730; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out;">
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