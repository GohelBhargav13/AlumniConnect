<?php
require './utills/db_conn.php';
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['click_btn'])) {
        $Entered_Enrollment = $_POST["Enrollment"] ?? 0;
        $Entered_Password = $_POST["password"] ?? '';
        $user_role = $_POST["role"];

        if ($user_role == "student") {
            $exist_user = "SELECT * FROM studentmaster WHERE Enrollment_no = ? AND req_status = 'accepted' ";
            $exist_user_stmt = $conn->prepare($exist_user);
            $exist_user_stmt->bind_param("i", $Entered_Enrollment);

            $exist_user_stmt->execute();

            $result = $exist_user_stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($Entered_Password, $user['student_password'])) {
                    $_SESSION['Enroll_no'] = $user['Enrollment_no'];
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['student_name'] = $user['student_name'];
                    $_SESSION['user_role'] = $_POST['role'];
                    header("Location: ./student/student_dashboard.php");
                    exit;
                } else {
                    $_SESSION['message'] = ["sucess" => false, "error_msg" => "Invalid Credential"];
                }
            } else {
                $_SESSION['message'] = ["sucess" => false, "error_msg" => 'Student not found'];
            }
        } else {

            if ($user_role == 'alumni') {
                $exist_user = "SELECT * FROM alumnimaster WHERE Enrollment_no = ? ";
                $exist_user_stmt = $conn->prepare($exist_user);
                $exist_user_stmt->bind_param("i", $Entered_Enrollment);

                $exist_user_stmt->execute();

                $result = $exist_user_stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    if (password_verify($Entered_Password, $user['alumni_password'])) {
                        $_SESSION['alumni_id'] = $user['alumni_id'];
                        $_SESSION['Enroll_no_alumni'] = $user['Enrollment_No'];
                        $_SESSION['alumni_name'] = $user['alumni_name'];
                        $_SESSION['user_role'] = $_POST['role'];
                        header("Location: ./alumni/alumni_dashboard.php");
                        exit;
                    } else {
                        $_SESSION['message'] = ["sucess" => false, "error_msg" => "Invalid Credential"];
                    }
                } else {
                    $_SESSION['message'] = ["sucess" => false, "error_msg" => 'Alumni not found'];
                }
            } else {
                $_SESSION['message'] = ['sucess' => false, 'mess' => 'Data is not found'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-blue: #007bff;
            --text-color: #333;
            --light-gray: #f0f2f5;
            --border-color: #ddd;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--light-gray);
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            height: 95vh;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .background-section {
            flex: 1.2;
            background: url('./uploads/website_images/login_image.jpg') no-repeat center center / cover;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            width: 50px;
            height: 500px;
            margin-left: 30px;
            margin-top: 40px;
        }

        .login-section {
            flex: 1;
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 20px;
            box-sizing: border-box;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .login-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 380px;
            text-align: center;
            z-index: 1;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo span {
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--text-color);
        }

        h2 {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 25px;
            font-weight: 500;
        }

        .form-group {
            text-align: left;
            position: relative;
            padding: 8px 8px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: calc(100% - 20px);
            padding: 12px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-color);
            outline: none;
            transition: border-color 0.2s;
            background-color: #fff;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-blue);
        }

        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='16' viewBox='0 0 24 24' width='16' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            cursor: pointer;
        }

        .password-group .password-toggle {
            position: absolute;
            right: 10px;
            top: 60%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            accent-color: var(--primary-blue);
        }

        .forgot-password {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .social-login-separator {
            position: relative;
            margin: 25px 0;
            text-align: center;
            color: #999;
            font-size: 0.9rem;
        }

        .social-login-separator span {
            background-color: #fff;
            padding: 0 10px;
            position: relative;
            z-index: 1;
        }

        .social-login-separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid var(--border-color);
            z-index: 0;
        }

        .btn-google {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            color: #666;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .btn-google img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .btn-google:hover {
            background-color: var(--light-gray);
            border-color: #c0c0c0;
        }

        .signup-link {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #666;
        }

        .signup-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                height: auto;
                max-width: 500px;
            }

            .background-section {
                height: 200px;
                border-radius: 20px 20px 0 0;
            }

            .login-section {
                padding: 20px;
                border-radius: 0 0 20px 20px;
            }

            .login-card {
                padding: 30px;
                box-shadow: none;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 20px;
            }

            h2 {
                font-size: 1.4rem;
            }

            .form-group input,
            .form-group select {
                padding: 10px 8px;
            }

            .btn-primary,
            .btn-google {
                padding: 10px;
                font-size: 1rem;
            }

            .freebie-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="background-section">
        </div>
        <div class="login-section">
            <div class="login-card">
                <div class="logo">
                    <img src="./uploads/website_images/download.png" alt="UI Unicorn Logo"> <span>UI Unicorn</span>
                </div>
                <h2>Nice to see you again</h2>

                <form action="./login.php" method="POST">
                    <div class="form-group">
                        <?php if (isset($_SESSION['message'])) : ?>
                            <?php if ($_SESSION['message']['sucess'] == false) { ?>
                                <p id="message" class="msg" style="
                                    color: red;
                                    background-color: #fdecea;
                                    border: 1px solid #f5c6cb;
                                    padding: 8px 10px;
                                    border-radius: 6px;
                                    font-size: 14px;
                                    font-weight: 500;
                                    margin-bottom: 10px;
                                    display: block;
                                    transition: all 0.3s ease-in-out;
                                ">
                                    <?= htmlspecialchars($_SESSION['message']['error_msg']) ?></p>
                            <?php } ?>
                            <script>
                                setTimeout(() => {
                                    let mess = document.getElementById('message');
                                    if (mess) mess.style.display = 'none';
                                }, 2 * 1000);
                            </script>
                            <?php unset($_SESSION['message']) ?>
                        <?php endif; ?>
                        <p id="message" class="msg"></p>
                        <p id="message" class="msg"></p>
                        <label for="email">Login</label>
                        <input type="number" id="Enrollment" name="Enrollment" placeholder="Enter Enrollment">
                    </div>
                    <div class="form-group password-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" maxlength="10">
                        <p id="strength_password"></p>
                    </div>
                    <div class="form-group select-wrapper">
                        <label for="role">Roles</label>
                        <select name="role" id="role">
                            <option value="">Select Role</option>
                            <option value="student">student</option>
                            <option value="alumni">alumni</option>
                        </select>
                    </div>


                    <div class="options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary" id="click_btn" name="click_btn" style="margin-bottom: 13px;">Sign In</button>
                </form>

                <div class="signup-link">
                    <span>Don't have an account? <a href="./student/student_register.php">Sign up now</a></span>
                </div>
            </div>
        </div>
    </div>
    <script>
        const message = document.getElementById("message");
        const Enrollment = document.getElementById("Enrollment");
        const password = document.getElementById("password");
        const userRole = document.getElementById("role");

        document.getElementById("click_btn").addEventListener("click", (e) => {

            function messageDisplay(text) {
                message.innerText = `${text} *`;
                message.style.color = "red";
                message.style.backgroundColor = "#fdecea";
                message.style.border = "1px solid #f5c6cb";
                message.style.padding = "8px 10px";
                message.style.borderradius = "6px";
                message.style.fontsize = "14px";
                message.style.fontweight = "500";
                message.style.marginbottom = "10px";
                message.style.display = "block";
                message.style.transition = "all 0.3s ease-in-out";

                setTimeout(() => {
                    message.style.display = 'none';
                    message.innerText = '';
                }, 2 * 1000);
            }

            let Enrollment_value = Enrollment.value;
            let password_value = password.value;
            let userRole_value = userRole.value;

            if (!Enrollment_value || !password_value || userRole_value == "") {
                e.preventDefault();
                messageDisplay("All fields are required");
                return;
            }

            if (Enrollment_value.length != 12) {
                e.preventDefault();
                messageDisplay("Enrollment must be 12 number");
                return;
            }

        })
    </script>
</body>

</html>